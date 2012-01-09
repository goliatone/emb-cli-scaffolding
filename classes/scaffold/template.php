<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * TODO Merge with CLI_Parameters. We use that class to initialize this one.
 * 
 */
class Scaffold_Template
{
	public $source_path;
	public $target_path;
	public $ext;
	
	public function __construct($source_path, $target_path)
	{
		$this->source_path = $source_path;
		$this->target_path = $target_path;
		//$ext = pathinfo($target_path, PATHINFO_EXTENSION);
	}
	
	
	
	public function __toString()
	{
		return "[Scaffold_Template => \nsource: {$this->source_path},\ntarget: {$this->target_path}]";
	}
	
}