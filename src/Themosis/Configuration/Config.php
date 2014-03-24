<?php
namespace Themosis\Configuration;

defined('DS') or die('No direct script access.');

/**
 * Parse the configuration values and call
 * a configuration Factory in order to install
 * them separately.
*/

class Config 
{	
	/**
	 * Reference to ALL configurations files.
	*/
	private static $configs = array();

	public function __construct($configFile)
	{
		static::$configs[] = $configFile;
	}

	/**
	 * Initialize configuration loading.
	 * 
	 * @param array
	 * @return boolean
	*/
	public static function make($configFiles)
	{
		if (!is_array($configFiles) || empty($configFiles)) return false;

		return static::parse($configFiles);
	}

	/**
	 * Install, read all configurations
	 * 
	 * @return int
	*/
	public static function set()
	{
		foreach (static::$configs as $config) {
			
			$factory = new ConfigFactory($config);
			$factory->dispatch();

		}

		return true;
	}

	/**
	 * Parse the array with configurations and will
	 * check if they exists.
	 * 
	 * @param array
	 * @return boolean
	*/
	private static function parse($configFiles)
	{
		$errors = array();

		foreach ($configFiles as $key => $configs) {
            
            foreach ($configs as $config) {
                
                $config = static::has($key, $config);

    			if ($config) {
    			    
    				new static($config);
    				
    			} else {
    				$errors[] = $config.' does not exists !';
    			}
                        
            }
            
		}

		if (count($errors) > 0) {
			return false;
		}

		return true;
	}

	/**
	 * Check if the config file exists inside the app/config
	 * directory.
	 * 
	 * @param string - key of the $GLOBALS themosis_paths.
	 * @param string - filename config file.
	 * @return mixed (array|boolean)
	*/
	private static function has($key, $configFile)
	{
		$path = themosis_path($key).'config'.DS.$configFile.CONFIG_EXT;

		if (file_exists($path)) {
			return array(
				'name'	=> $configFile,
				'path'	=> $path
			);
		}

		return false;
	}
}