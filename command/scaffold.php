<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Command_Scaffold class a simple command to clear cache
 * TODO Add real extension to templates, and then just remove it!
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
		$this->index();
		
		//get --project=embcrud => Project template we use.
		$project = $options->get('project','embcrud');
		
		//We need to find the project template in the filesytem.
		$source_dir = MODPATH.'emb-cli-scaffolding'.DS.'templates'.DS.$project;
		
		//We load default values for scaffold from out project template.
		$config = Helper_Scaffold::get_config($source_dir);
		
		//get --output=mymodule => this specified the project template
		$output  = $options->get('output',$config->command_options['output']);
		$target_dir = MODPATH.'emb-cli-scaffolding'.DS.$output;
		
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
			CLI::error($e->getMessage());
			return;
		}
		
		$templates = Helper_Scaffold::generate_templates($source_dir, $target_dir, $args);
		
		//CLI::write(Kohana_Debug::dump($templates));
		
		foreach($templates as $template)
		{
			Helper_Scaffold::replace_template($template,$args,TRUE);
		}
		
	}
	
}