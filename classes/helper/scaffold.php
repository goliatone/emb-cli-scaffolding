<?php defined('SYSPATH') OR die('No direct script access.');
/**
 *
 */
class Helper_Scaffold
{
	/**
	 * 
	 */
	public static function get_config($source_dir, $config_file_name = 'scaffolding')
	{
		Kohana::$config->attach(new Kohana_Config_File($source_dir));
		return Kohana::$config->load($config_file_name);
	}
	
	/**
	 * 
	 */
	public static function generate_templates($source_dir, $target_dir, $args, array $config = array('.tpl' => '.php'), array $exclude = array('scaffolding.php'))
	{
		$source_dir = Helper_File::ensure_trailing_slash($source_dir);
		$target_dir = Helper_File::ensure_trailing_slash($target_dir);
		
		if(! Helper_File::assert_directory(array($source_dir,$target_dir)) ) return FALSE;
		
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
		if( ! is_file($output_filename))
		{
			Helper_File::ensure_file_path($output_filename);
			file_put_contents($output_filename, $template);
		} 
		else if($overwrite)
		{
			//Use Tiny_diff			
			$tiny_diff = new Tiny_diff();

			// Get the strings to compare
			$file_new = $template;
			$file_old = file_get_contents($output_filename);
			
			if($file_new === $file_old)
			{
				 file_put_contents($output_filename, $template);
				 return;
			}
			
			CLI::write("Warning, owerwriting file".PHP_EOL);
			
			// Experimental!
			$difference = $tiny_diff->compare($file_new, $file_old, 'normal');
			$overwrite = CLI::read("Do you want to overwrite file {$output_filename} ?", array('y','n'));
			
			if($overwrite === 'y')
			{
				//TODO Move, clean, refactor.
				$ext = pathinfo($output_filename, PATHINFO_EXTENSION);
				$old_filename = str_replace($ext,'old.'.$ext,$output_filename);
				$new_filename = str_replace($ext,'new.'.$ext,$output_filename);
				file_put_contents($old_filename, $file_old);
				file_put_contents($new_filename, $file_new);
				
				//How do we make it so that newlines and spaces are ignored
				file_put_contents($output_filename,$difference);
			} 
			else
			{
				return;
			}
		}
	}
	
	/**
	 * 
	 */
	static public function check_target_dir($output, $module)
	{
		//Check if $output is a valid dir i.e. entered full path.
		if(is_dir($output)) return $output;
		else return MODPATH.$module.DS.$output;
	}
}	