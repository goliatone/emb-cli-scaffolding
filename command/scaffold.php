<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Command_Scaffold class a simple command to clear cache
 * TODO 
 * - Fix resources singular, uppercase, plural et al for templates.
 * - Have Merge Scaffold_Template with CLI_Parameters. We use that class to initialize this one.
 * - Add real extension to templates, and then just remove it!
 * - Extends CLI::read to use CLI_Paramters, so we can pass any number of arguments.
 * 
 * @package    OpenBuildings/timestamped-migrations
 * @author     Ivan Kerin
 * @copyright  (c) 2011 OpenBuildings Inc.
 * @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
 **/
define('DS',DIRECTORY_SEPARATOR);

class Command_Scaffold extends Command
{
	const VERSION = "0.0.1";
	
	public function test()
	{
		$o = Kohana::find_file('themes', 'default/config/test');
		Kohana::$config->attach(new Kohana_Config_File('themes/default/config'));
		$t = Kohana::$config->load('test');
		CLI::write($t->test);
	}
	
	public function index(Command_Options $options = NULL)
	{
		CLI::write("*************************************".PHP_EOL);
		CLI::write("Scaffold v".Command_Scaffold::VERSION);
		CLI::new_line();
		CLI::write("Template scaffolding command.");
		CLI::new_line();
		CLI::write("*************************************".PHP_EOL);
	}
	
	public function create(Command_Options $options)
	{
		$this->index($options);
		
		//get --project=embcrud => Project template we use.
		$project = $options->get('project','embcrud');
		
		//We need to find the project template in the filesytem.
		$config_dir = 'templates'.DS.$project.DS.'config';
		
		//We load default values for scaffold from out project template.
		$config = Helper_Scaffold::get_config($config_dir);
		
		//get --output=mymodule => this specified the project template
		$output  = $options->get('output',$config->command_options['output']);
		
		//get --output=mymodule => this specified the project template
		$module  = $options->get('module',$config->command_options['module']);
		
		//TODO 
		$target_dir = Helper_Scaffold::check_target_dir($output, $module);
		
		
		/* For some reason, this does not work from Helper_Scaffold::generate_templates
		 * Make sure we have the target path:
		 */
		Helper_File::ensure_file_path($target_dir.DS.'ensure.php');
		
		try
		{
			$args = CLI_Parameters::factory($config->defaults, $config->required);
		}
		catch(Kohana_Exception $e)
		{
			//Most likely, we are missing a required argument.
			CLI::error($e->getMessage());
			return;
		}
		
		/*
		 * List of ignored files inside our project template.
		 */
		$ignored_files = $options->get('ignore', $config->command_options['ignore']);
		
		/*
		 * Options for the constructions of template paths.
		 * We replace .tpl with .php
		 * We should actually have our template have the final
		 * extension plus the tpl, and just remove the tpl.
		 * index.php.tpl => index.php.tpl
		 * style.css.tpl => style.css 
		 */
		$decoration_config = array('.tpl' => '.php');
		
		/*
		 * We need to actually make this a bit more functional.
		 */
		$templates_module = 'emb-cli-scaffolding';
		$source_dir = MODPATH.$templates_module.DS.'templates'.DS.$project;
		
		$templates = Helper_Scaffold::generate_templates($source_dir, $target_dir, $args, $decoration_config, $ignored_files);
		
		if($templates === FALSE)
		{
			CLI::error("Error creating templates. Check your directories");
			return;
		}
		
		//CLI::write(Kohana_Debug::dump($templates));
		//return;
		
		foreach($templates as $template)
		{
			Helper_Scaffold::replace_template($template,$args,TRUE);
		}
		
	}
	
}