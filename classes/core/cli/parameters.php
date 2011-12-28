<?php defined('SYSPATH') OR die('No direct script access.');
/**
 *
 */
abstract class Core_CLI_Parameters implements ArrayAccess, Iterator, Countable 
{
	private $_arguments = array();
	
	/**
	 * 
	 */
	public function __construct(array $defaults = array())
	{
		foreach($defaults as $key => $value)
		{
			$this->offsetSet($key, $value);
		}
	}
	
	/**
	 * 
	 */
	public function compile()
	{
		for ($i = 2; $i < $_SERVER['argc']; $i++)
		{
			if ( ! isset($_SERVER['argv'][$i]))
			{
				// No more args left
				break;
			}

			// Get the option
			$opt = $_SERVER['argv'][$i];

			if (substr($opt, 0, 2) === '--')
			{
				// This is an option argument
				continue;
			}

			if (strpos($opt, '='))
			{
				// Separate the name and value
				list ($opt, $value) = explode('=', $opt, 2);
				$value = $this->_parse_values($value);
			}
			else
			{
				$value = TRUE;
			}

			$this->offsetSet($opt, $value);
		}
		return $this;
	}
	
	/**
	 * 
	 */
	protected function _parse_values($arg)
	{
		if(!strpos($arg, ',')) return $arg;
		
		$out = array();
		$values = explode(',',$arg);
		
		foreach($values as $value)
		{
			if (strpos($value, ':'))
			{
				list ($key, $value) = explode(':', $value, 2);
				$out[$key] = $value;
			}
			else
			{
				$out[] = $value;
			}
		}
		
		return $out;
	}
	
	public function get($key,$default=NULL)
	{
		if($this->has($key)) return $this->offsetGet($key);
		
		return $default;
	}
	
	public function set($key,$value)
	{
		$this->offsetSet($key, $value);
	}
	
	/**
	 * Magic!! We use this methods to check for stuff in 
	 * the template.
	 * - capitalize_XX
	 * - pluralize_XX
	 * - singularize_XX
	 * 
	 * -has_XX_not_VAL
	 * -has_XX_yes_VAL
	 */
	public function __get($key)
	{
		if(strpos($key,'has_') !== FALSE)
		{
			list($has, $key) = explode('_', $key, 2);
		}
		
		return $this->offsetGet($key);
	}
	
	/**
	 * THIS MAGIC DOES NOT WORK WITH MUSTACHE SINCE IT CHECKS 
	 * WITH method_exists, AND THAT WONKS IT. 
	 *
	 * Magic!! We use this methods to check for stuff in 
	 * the template.
	 * - capitalize_XX
	 * - pluralize_XX
	 * - singularize_XX
	 * 
	 * -has_XX_not_VAL
	 * -has_XX_yes_VAL
	 */
	public function __call($method,$args)
	{
		if(strpos($method,'has_') !== FALSE)
		{
			list($has, $key) = explode('_', $method, 2);
			return $this->has($key);
		}
	}
	
	/**
	 * 
	 */
	public function __isset($name)
    {
    	if(strpos($name,'has_') !== FALSE)
		{
			list($has, $name) = explode('_', $name, 2);
		}
		
        return isset($this->_arguments[$name]);
    }
	
	//////////////////////////////////
	
	/**
	 * 
	 */
	public function has($name)
	{
		return array_key_exists($name, $this->_arguments);
	}
	/**
	 * Return the raw attributes array
	 * @return array
	 */
	public function as_array()
	{
		return $this->_arguments;
	}
	
	public function as_tpl_array()
	{
		$out = array();
		foreach($this->_arguments as $key => $value)
		{
			$out['{'.$key.'}'] = $value;
		}
		return $out;
	}
	
	/**
	 * 
	 */
	public function offsetSet($offset, $value) 
	{
		if ($offset == "") 
		{
			$this->_arguments[] = $value;
		}
		else 
		{
			$this->_arguments[$offset] = $value;
		}
	}
	
	/**
	 * 
	 */
	public function offsetExists($offset) 
	{
	 return isset($this->_arguments[$offset]);
	}
	
	/**
	 * 
	 */
	public function offsetUnset($offset) 
	{
		unset($this->_arguments[$offset]);
	}
	
	/**
	 * 
	 */
	public function offsetGet($offset) 
	{
		return isset($this->_arguments[$offset]) ? $this->_arguments[$offset] : null;
	}
	
	/**
	 * 
	 */
	public function rewind() 
	{
		reset($this->_arguments);
	}
	
	/**
	 * 
	 */
	public function current() 
	{
		return current($this->_arguments);
	}
	
	/**
	 * 
	 */
	public function key() 
	{
		return key($this->_arguments);
	}
	
	/**
	 * 
	 */
	public function next() 
	{
		return next($this->_arguments);
	}
	
	/**
	 * 
	 */
	public function valid() 
	{
		return $this->current() !== false;
	}    
	
	/**
	 * 
	 */
	public function count() 
	{
	 return count($this->_arguments);
	}

}