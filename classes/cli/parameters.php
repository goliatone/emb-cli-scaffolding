<?php defined('SYSPATH') OR die('No direct script access.');
/**
 *
 */
class CLI_Parameters extends Core_CLI_Parameters
{
	/**
	 * 
	 * @param 	array 	$defaults Default values for parameters.
	 * @throws 	Kohana_Exception	If we are missing a required arugment.
	 */
	public static function factory(array $defaults = array(), array $required = array() )
	{
		$args = new CLI_Parameters($defaults);
		$args->compile();
		
		foreach($required as $key)
		{
			if(! $args->has($key)) throw new Kohana_Exception("Missing required argument: :name", array(":name" => $key));
		}
		
		return $args;
	}
}