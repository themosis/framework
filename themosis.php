<?php
/*
Plugin Name: Themosis framework
Plugin URI: http://www.themosis.com/
Description: A framework for WordPress developers.
Version: 0.9
Author: Julien Lambé
Author URI: http://jlambe.be/en/
License: GPLv2
*/

/*----------------------------------------------------*/
// The directory separator
/*----------------------------------------------------*/
defined('DS') ? DS : define('DS', DIRECTORY_SEPARATOR);

/*----------------------------------------------------*/
// Plugin core textdomain
/*----------------------------------------------------*/
defined('THEMOSIS_PLUGIN_TEXTDOMAIN') ? THEMOSIS_PLUGIN_TEXTDOMAIN : define('THEMOSIS_PLUGIN_TEXTDOMAIN', 'themosis-plugin');

/**
 * Helper function to retrieve the path.
 *
 * @param string
*/
if (!function_exists('themosis_path'))
{
    function themosis_path($name)
    {
        return $GLOBALS['themosis_paths'][$name];
    }
}

/**
 * Main class that bootstraps the framework.
 * 
*/
class THFWK_Themosis
{
	/**
	 * Framework bootstrap instance
	*/
	private static $instance = null;

	/**
	 * Framework version
	*/
	const VERSION = 0.9;

	/**
	 * Plugin directory name
	 * 
	 * @var string
	*/
	private static $dirName = '';

	private function __construct()
	{
		static::$dirName = static::setDirName(__DIR__);

        // Perform plugin load...
		$this->load();
	}

	/**
	 * Init the framework classes
	*/
	public static function getInstance()
	{
		if (is_null(static::$instance))
        {
	    	static::$instance = new static();
	    }
	 	return static::$instance;
	}

    /**
     * Set the plugin directory property. This property
     * is used as 'key' in order to retrieve the plugins
     * informations.
     *
     * @param string
     * @return string
    */
    private static function setDirName($path)
    {
    	$dirName = explode('plugins', $path);
    	$dirName = substr($dirName[1], 1);

    	return $dirName;
    }

    /**
     * Display a notice in the administration.
     *
     * @return void
     */
    public function displayMessage()
    {
    ?>
        <div id="message" class="error">
            <p><?php _e(sprintf('<b>Themosis plugin:</b> %s', "Symfony Class Loader component not found. Make sure to include it before proceeding."), THEMOSIS_PLUGIN_TEXTDOMAIN); ?></p>
        </div>
    <?php
    }

	/**
	 * Load the framework classes.
     *
     * @return void
	*/
	private function load()
	{
        if (!class_exists('Symfony\Component\ClassLoader\ClassLoader'))
        {
            add_action('admin_notices', array($this, 'displayMessage'));

            return;
        }

        // Autoload PSR-0 classes.
        $loader = new \Symfony\Component\ClassLoader\ClassLoader();
        $loader->addPrefixes(array(
            'Themosis' => __DIR__.DS.'src'.DS
        ));
        $loader->register();

		// Set the framework paths and starts the framework.
		add_action('after_setup_theme', array($this, 'bootstrap'));
	}

	/**
	 * Define paths and bootstrap the framework.
	 *
     * @return void
	*/
	public function bootstrap()
    {
		/**
	    * Define all framework paths
	    * These are real paths, not URLs to the framework files.
	    * These paths are extensible with the help of WordPress
	    * filters.
	    */
	    // All framework paths
	    $paths = apply_filters('themosis_framework_paths', array());

	    // Framework core path
	    $paths['sys'] = realpath(__DIR__).DS.'src'.DS.'Themosis'.DS;

	    // Application datas - This plugin 'app' folder
    	$paths['datas'] = realpath(__DIR__).DS.'app'.DS;

        // Application admin folder
        $paths['admin'] = realpath(__DIR__).DS.'app'.DS.'admin'.DS;

        // Application storage directory
        $paths['storage'] = realpath(__DIR__).DS.'app'.DS.'storage'.DS;

	    // Register globally the paths
	    foreach ($paths as $name => $path)
        {
	       if (!isset($GLOBALS['themosis_paths'][$name]))
           {
	           $GLOBALS['themosis_paths'][$name] = $path;
	       }
	    }

	    // Start the framework
	    if (isset($GLOBALS['THFWK_Themosis']))
        {
	        require_once themosis_path('sys').'Core'.DS.'Start.php';
	    }
	}

	/**
	 * Returns the directory name.
	 * 
	 * @return string
	*/
	public static function getDirName()
    {
		return static::$dirName;
	}
}

/**
 * Load the main class.
*/
add_action('plugins_loaded', function(){

	$GLOBALS['THFWK_Themosis'] = THFWK_Themosis::getInstance();

});