<?php defined('SYSPATH') OR die('No direct script access.');
/**
 *
 */
class Helper_Scaffold
{
	/**
	 * 
	 */
	public static function get_config($source_dir, $config_file_name = 'config.php', $as_array = FALSE)
	{
		$project	= Helper_File::get_parent_dir($source_dir.DIRECTORY_SEPARATOR.$config_file_name);
		$source_dir = Helper_File::ensure_trailing_slash($source_dir).$config_file_name;
		
		if(!is_file($source_dir)) return FALSE;
		
		$source = Kohana::load($source_dir);
		
		if($as_array) return $source;
		
		return new Config_Group(new Kohana_Config, $project, $source);
	}
	
	
	/**
	 * 
	 */
	public static function generate_templates($source_dir, $target_dir, $args, array $config = array('.tpl' => '.php'), array $exclude = array('config.php'))
	{
		$source_dir = Helper_File::ensure_trailing_slash($source_dir);
		$target_dir = Helper_File::ensure_trailing_slash($target_dir);
		
		$config[$source_dir] = $target_dir;
		
		$files 		= Helper_File::get_filenames($source_dir, TRUE, TRUE, $exclude );
		$templates	= Helper_Scaffold::replace_decorations($files, $args, $config);
		
		return $templates;
	}
	
	
	/**
	 * 
	 * @param	array 	$files
	 * @param	array 	$decorations
	 */
	public static function replace_decorations(array $files, $decorations, $config)
	{
		$out = array();
		$m = new Mustache;
		
		foreach( $files as $key => $path)
		{
			$clean_path = $m->render($path, $decorations);
			$clean_path = strtr($clean_path,$config);
			//$out[$key] =  $clean_path;
			$out[$key] = new Scaffold_Template($path,$clean_path);
		}
		
		return $out;
	}
	
	/**
	 * 
	 */
	static public function replace_template(Scaffold_Template $template, $decorations = null, $overwrite = FALSE )
	{
		//$template_filename = Kohana::find_file('templates', $name, 'tpl');
		$template_filename = $template->source_path;
		$output_filename   = $template->target_path;
		if( ! $template_filename )
			throw new Kohana_Exception("Kohana Exception does not exist: :name", array(":name" => $template_filename));
		
		$template = file_get_contents($template_filename);
		
		if( $decorations )
		{
			//TODO Move this into the Scaffold_Template, so that we can override how we replace stuff! 
			$m = new Mustache;
			$template = $m->render($template, $decorations);
			//$template = strtr($template, $decorations);   
		}
		
		//Use Tiny_diff to merge changes!
		if( ! is_file($output_filename) || $overwrite)
		{
			Helper_File::ensure_file_path($output_filename);
			file_put_contents($output_filename, $template);
		}
	}
}	