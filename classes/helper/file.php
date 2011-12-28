<?php defined('SYSPATH') OR die('No direct script access.');
/**
 *
 */
class Helper_File 
{
	/**
	 * Get Filenames, ported from CodeIgniter.
 	 *
 	 * Reads the specified directory and builds an array containing the filenames.
 	 * Any sub-folders contained within the specified path are read as well.
	 *
	 * @param	string 	$source_dir	path to source
	 * @param	boolean	$include_path	whether to include the path as part of the filename
	 * @param	boolean	$recursion	 internal variable to determine recursion status - do not use in calls
	 * 
	 * @see	http://codeigniter.com/user_guide/helpers/file_helper.html
	 * @return  array
	 */
	static public function get_filenames($source_dir, $include_path = FALSE, $recursion = FALSE, array $ignore =array(), array &$filedata = array())
    {
		$source_dir = self::ensure_trailing_slash($source_dir);
		
        if ($fp = @opendir($source_dir))
        {
            // reset the array and make sure $source_dir has a trailing slash on the initial call
            if ($recursion === FALSE)
            {
                $filedata = array();                
            }

            while (FALSE !== ($file = readdir($fp)))
            {
                if (@is_dir($source_dir.$file) && strncmp($file, '.', 1) !== 0)
                {
                    self::get_filenames($source_dir.$file.DIRECTORY_SEPARATOR, $include_path, TRUE, $ignore, $filedata);
                }
                elseif (strncmp($file, '.', 1) !== 0)
                {
                	if(in_array($file,$ignore) || in_array($source_dir.$file,$ignore) ) continue;
                    $filedata[] = ($include_path == TRUE) ? $source_dir.$file : $file;
                }
            }
            return $filedata;
        }
        else
        {
            return FALSE;
        }
    }
	
	/**
	 * @param	string 	$source_dir	path to clean
	 */
	static public function ensure_trailing_slash($source_dir)
	{
		return rtrim(realpath($source_dir), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
	}
	
	/**
	 * 
	 */
	static public function file_path($file_path)
	{
		return str_replace(basename($file_path),'',$file_path);
		//return strtr($file_path,basename($file_path));
	}
	
	/**
	 * 
	 */
	static public function ensure_file_path($file_path, $chmode = 0777)
	{
		$path = self::file_path($file_path);
		
		if( ! is_dir($path)) mkdir($path, $chmode, TRUE);
		
		return $file_path;
	}
	
	/**
	 * 
	 */
	static public function get_parent_dir($file_path)
	{
		$dirs = explode(DIRECTORY_SEPARATOR,$file_path);
		array_pop($dirs);
		//TODO Check for file extension, or ensure we have file.
		return array_pop($dirs);
		
	}
}