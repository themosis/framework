<?php
/*
Plugin Name: Themosis framework
Plugin URI: http://framework.themosis.com/
Description: A framework for WordPress developers.
Version: 1.2.2
Author: Julien Lambé
Author URI: http://www.themosis.com/
License: GPLv2
*/

/*----------------------------------------------------*/
// The directory separator.
/*----------------------------------------------------*/
defined('DS') ? DS : define('DS', DIRECTORY_SEPARATOR);

/*----------------------------------------------------*/
// Framework textdomain.
/*----------------------------------------------------*/
defined('THEMOSIS_FRAMEWORK_TEXTDOMAIN') ? THEMOSIS_FRAMEWORK_TEXTDOMAIN : define('THEMOSIS_FRAMEWORK_TEXTDOMAIN', 'themosis-framework');

/*----------------------------------------------------*/
// Storage path.
/*----------------------------------------------------*/
defined('THEMOSIS_STORAGE') ? THEMOSIS_STORAGE : define('THEMOSIS_STORAGE', WP_CONTENT_DIR.DS.'storage');

/**
 * Helper function to set the paths.
 *
 * @param array
 * @return void
 */
if (!function_exists('themosis_set_paths'))
{
    function themosis_set_paths(array $paths)
    {
        foreach ($paths as $name => $path)
        {
            if (!isset($GLOBALS['themosis_paths'][$name]))
            {
                $GLOBALS['themosis_paths'][$name] = realpath($path).DS;
            }
        }
    }
}

/**
 * Helper function to retrieve the path.
 *
 * @param string
 * @return string
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
 */
if (!class_exists('THFWK_Themosis'))
{
    class THFWK_Themosis
    {
        /**
         * Framework bootstrap instance.
         *
         * @var \THFWK_Themosis
         */
        protected static $instance = null;

        /**
         * Framework version.
         *
         * @var float
         */
        const VERSION = '1.2.2';

        /**
         * Plugin directory name.
         *
         * @var string
         */
        protected static $dirName = '';

        protected function __construct()
        {
            static::$dirName = static::setDirName(__DIR__);

            // Load plugin.
            $this->load();
        }

        /**
         * Init the framework classes
         *
         * @return \THFWK_Themosis
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
        protected static function setDirName($path)
        {
            $parent = static::getParentDirectoryName(dirname($path));

            $dirName = explode($parent, $path);
            $dirName = substr($dirName[1], 1);

            return $dirName;
        }

        /**
         * Check if the plugin is inside the 'mu-plugins'
         * or 'plugin' directory.
         *
         * @param string $path
         * @return string
         */
        protected static function getParentDirectoryName($path)
        {
            // Check if in the 'mu-plugins' directory.
            if (WPMU_PLUGIN_DIR === $path)
            {
                return 'mu-plugins';
            }

            // Install as a classic plugin.
            return 'plugins';
        }

        /**
         * Load the framework classes.
         *
         * @return void
         */
        protected function load()
        {
            // Default path to Composer autoload file.
            $autoload = __DIR__.DS.'vendor'.DS.'autoload.php';

            // Check for autoload file in dev mode (vendor loaded into the plugin)
            if (file_exists($autoload))
            {
                require($autoload);
            }

            // Set the framework paths and starts the framework.
            $this->bootstrap();
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
            // Framework paths.
            $paths = apply_filters('themosis_framework_paths', []);

            // Plugin base path.
            $paths['plugin'] = __DIR__.DS;

            // Framework base path.
            $paths['sys'] = __DIR__.DS.'src'.DS.'Themosis'.DS;

            // Storage path.
            $paths['storage'] = THEMOSIS_STORAGE;

            // Register globally the paths
            themosis_set_paths($paths);

            // Bootstrap the framework
            require_once themosis_path('plugin').'bootstrap'.DS.'start.php';
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
}

/**
 * Load the main class.
 *
 */
$GLOBALS['THFWK_Themosis'] = THFWK_Themosis::getInstance();