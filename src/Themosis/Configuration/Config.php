<?php
namespace Themosis\Configuration;

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

    /**
     * The Config constructor.
     *
     * @param array $configFile The configuration file properties.
     */
	public function __construct(array $configFile)
	{
		static::$configs[] = $configFile;
	}

    /**
     * Initialize configuration loading.
     *
     * @param array $configFiles The configuration files.
     * @return bool True. False if errors.
     */
	public static function make(array $configFiles)
	{
		if (!is_array($configFiles) || empty($configFiles)) return false;

		return static::parse($configFiles);
	}

	/**
	 * Install, read all configurations
	 * 
	 * @return void
	 */
	public static function set()
	{
		foreach (static::$configs as $config)
        {
			$factory = new ConfigFactory($config);
			$factory->dispatch();
		}
	}

	/**
	 * Parse the array with configurations and will
	 * check if they exists.
	 * 
	 * @param array $configFiles The configuration files.
	 * @return bool True. False if errors.
	 */
	private static function parse(array $configFiles)
	{
		$errors = array();

		foreach ($configFiles as $key => $configs)
        {
            foreach ($configs as $config)
            {
                $config = static::has($key, $config);

    			if ($config)
                {
    				new static($config);
    			}
                else
                {
    				$errors[] = $config.' does not exists !';
    			}
            }
		}

		if (count($errors) > 0)
        {
			return false;
		}

		return true;
	}

	/**
	 * Check if the config file exists inside the app/config
	 * directory.
	 * 
	 * @param string $key Key of the $GLOBALS themosis_paths.
	 * @param string $configFile Filename of the config file.
	 * @return array|bool Array if successful, false if errors.
	 */
	private static function has($key, $configFile)
	{
		$path = themosis_path($key).'config'.DS.$configFile.CONFIG_EXT;

		if (file_exists($path))
        {
			return array(
				'name'	=> $configFile,
				'path'	=> $path
			);
		}

		return false;
	}
}