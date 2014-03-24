<?php
/*
Plugin Name: Themosis framework
Plugin URI: http://themosis.com/
Description: A PHP framework for WordPress developers.
Version: 0.8
Author: Julien Lambé
Author URI: http://jlambe.be/en/
License: GPLv2
*/

/*----------------------------------------------------*/
// The directory separator
/*----------------------------------------------------*/
defined('DS') ? DS : define('DS', '/');

/**
 * Helper function to retrieve the path.
 *
 * @param string
*/
function themosis_path($name){
	return $GLOBALS['themosis_paths'][$name];
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
	private $version = 0.8;

	/**
	 * Plugin directory name
	 * 
	 * @var string
	*/
	private static $dirName = '';

	private function __construct()
	{
		static::$dirName = static::setDirName(__DIR__);
		$this->load();
	}

	/**
	 * Init the framework classes
	*/
	public static function getInstance()
	{
		if (is_null(static::$instance)) {
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
	 * Load the framework classes
	*/
	private function load()
	{
		// Insert Composer autoloading feature.
		require 'vendor/autoload.php';

		// Set the framework paths and starts the framework.
		add_action('after_setup_theme', array($this, 'bootstrap'));
	}

	/**
	 * Define paths and bootstrap the framework
	 * 
	*/
	public function bootstrap(){

		/**
	    * Define all framework paths
	    * These are real paths, not URLs to the framework files.
	    * These paths are extensibles with the help of Wordpress
	    * filters.
	    */
	    // All framework paths
	    $paths = apply_filters('themosis_framework_paths', array());

	    // Framework core path
	    $paths['sys'] = realpath(__DIR__).DS.'src'.DS.'Themosis'.DS;

	    // Application datas - This plugin 'app' folder
    	$paths['datas'] = realpath(__DIR__).DS.'app'.DS;

	    // Register globally the paths
	    foreach ($paths as $name => $path) {
	       if(!isset($GLOBALS['themosis_paths'][$name])){
	           $GLOBALS['themosis_paths'][$name] = $path;
	       }
	    }

	    // Start the framework
	    if (isset($GLOBALS['THFWK_Themosis'])) {

	        require_once themosis_path('sys').'Core'.DS.'Start.php';

	    }
	}

	/**
	 * Returns the directory name.
	 * 
	 * @return string
	*/
	public static function getDirName(){

		return static::$dirName;

	}
}

/**
 * Load the main class
*/
add_action('plugins_loaded', function(){

	$GLOBALS['THFWK_Themosis'] = THFWK_Themosis::getInstance();

});

?>