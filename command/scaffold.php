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
	
	public function index(Command_Options $options)
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
		//get --project=embcrud => Project template we use.
		$project = $options->get('project','embcrud');
		
		//We need to find the project template in the filesytem.
		$source_dir = MODPATH.'emb-cli-scaffolding'.DS.'templates'.DS.$project;
		
		//We load default values for scaffold from out project template.
		$config = Helper_Scaffold::get_config($source_dir);
		
		//get --output=mymodule => this specified the project template
		$output  = $options->get('output',$config->command_options['output']);
		$target_dir = MODPATH.'emb-cli-scaffolding'.DS.$output;
		
		
		try
		{
			$args = CLI_Parameters::factory($config->defaults, $config->required);
		}
		catch(Kohana_Exception $e)
		{
			CLI::error($e->getMessage());
			return;
		}
		
		//$args->set('resource','resource');
		
		$templates = Helper_Scaffold::generate_templates($source_dir, $target_dir, $args);
		
		foreach($templates as $template)
		{
			Helper_Scaffold::replace_template($template,$args,TRUE);
		}
		
	}
	
	private function _ensure_file_path($module_name,$output_filename,$ext = '.php')
	{
		$file_path = MODPATH.$module_name.DIRECTORY_SEPARATOR.'output'.DIRECTORY_SEPARATOR.$output_filename.$ext;
		
		$path = explode(DIRECTORY_SEPARATOR,$file_path);
		array_pop($path);
		$path = implode(DIRECTORY_SEPARATOR,$path);
		
		if( ! is_dir($path)) mkdir($path,0777, true);
		
		return $file_path;
	}
	
	static public function set_template($filename, $name, $decorations = null,$overwrite=FALSE)
	{
		$template_filename = Kohana::find_file('templates', $name, 'tpl');

		if( ! $template_filename )
			throw new Kohana_Exception("Kohana Exception does not exist: :name", array(":name" => $name));
		
		$template = file_get_contents($template_filename);

		if( $decorations )
		{
			$m = new Mustache;
			$template = $m->render($template, $decorations);
			//$template = strtr($template, $decorations);   
		}
		
		if( ! is_file($filename) || $overwrite) file_put_contents($filename, $template);
	}
}