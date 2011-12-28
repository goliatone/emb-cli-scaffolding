<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Command_Migration class a simple command interface with the Kohana_Migrations module.
 *
 * @package    OpenBuildings/timestamped-migrations
 * @author     Ivan Kerin
 * @copyright  (c) 2011 OpenBuildings Inc.
 * @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
 **/
class Command_Migration extends Command
{

	const MIGRATE_BRIEF = "Generate migrations";
	const MIGRATE_DESC  = "Generate a module with ./kohana module:generate <module-name> Optional params are --guide and --init which will generate those stubs of the module for you";
	/**
	 * 
	 */
	public function migrate(Command_Options $options)
	{
		//self::log_func(array(Cache::instance(), 'delete_all'), null, Command::OK);
		//self::log_func("system", array("rm -rf ".Kohana::$cache_dir."/*"), Command::OK);
		$out = Kohana_Debug::dump($options->as_array());
		CLI::write($out);
	}
	
	/**
	 * 
	 */
	public function up(Command_Options $options)
	{
		$class_name = $this->_get_class_name($options);		
		
		system("php index.php --uri=migrate/up/$class_name");
	}
	
	/**
	 * 
	 */
	public function down(Command_Options $options)
	{
		$class_name = $this->_get_class_name($options);		
		
		system("php index.php --uri=migrate/down/$class_name");
	}
	
	/**
	 * 
	 */
	public function create(Command_Options $options)
	{
		CLI::write("We are creating stuff");
		CLI::new_line();
		
		$class_name = $this->_get_class_name($options);		
		
		//self::log_func("system", array("php index.php --uri=migrate/create/$class_name"), Command::OK);
		system("php index.php --uri=migrate/create/$class_name");
	}
	
	public function index(Command_Options $options)
	{
		//self::log_func("system", array("php index.php --uri=migrate/index"), Command::OK);
		system("php index.php --uri=migrate/index");
	}
	
	public function seed(Command_Options $options)
	{
		//self::log_func("system", array("php index.php --uri=migrate/seed"), Command::OK);
		system("php index.php --uri=migrate/seed");
	}
	
	private function _get_class_name(Command_Options $options)
	{
		if(! $options->class) 
		{
			CLI::error("Need to specify the migration name.");
			return;
		}
		
		$class_name = $options->class;
		if(!strpos($class_name,'Migration')) $class_name = $class_name.'Migration';
		
		return $class_name;
	}
}
