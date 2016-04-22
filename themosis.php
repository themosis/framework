<?php
/*
Plugin Name: Themosis framework
Plugin URI: http://framework.themosis.com/
Description: A framework for WordPress developers.
Version: 1.2.3
Author: Julien Lambé
Author URI: http://www.themosis.com/
License: GPLv2
*/

/*----------------------------------------------------*/
// The directory separator.
/*----------------------------------------------------*/
defined('DS') ? DS : define('DS', DIRECTORY_SEPARATOR);

/*----------------------------------------------------*/
// Themosis framework textdomain.
/*----------------------------------------------------*/
defined('THEMOSIS_FRAMEWORK_TEXTDOMAIN') ? THEMOSIS_FRAMEWORK_TEXTDOMAIN : define('THEMOSIS_FRAMEWORK_TEXTDOMAIN', 'themosis-framework');

/*----------------------------------------------------*/
// Storage path.
/*----------------------------------------------------*/
defined('THEMOSIS_STORAGE') ? THEMOSIS_STORAGE : define('THEMOSIS_STORAGE', WP_CONTENT_DIR.DS.'storage');

if (!function_exists('themosis_set_paths')) {
    /**
     * Register paths globally.
     *
     * @param array $paths Paths to register using alias => path pairs.
     */
    function themosis_set_paths(array $paths)
    {
        foreach ($paths as $name => $path) {
            if (!isset($GLOBALS['themosis.paths'][$name])) {
                $GLOBALS['themosis.paths'][$name] = realpath($path).DS;
            }
        }
    }
}

if (!function_exists('themosis_path')) {
    /**
     * Helper function to retrieve a previously registered path.
     *
     * @param string $name The path name/alias.
     *
     * @return string
     */
    function themosis_path($name)
    {
        return $GLOBALS['themosis.paths'][$name];
    }
}

/*
 * Main class that bootstraps the framework.
 */
if (!class_exists('Themosis')) {
    class themosis
    {
        /**
         * Themosis instance.
         *
         * @var \Themosis
         */
        protected static $instance = null;

        /**
         * Framework version.
         *
         * @var float
         */
        const VERSION = '1.2.3';

        private function __construct()
        {
            $this->initialize();
            $this->bootstrap();
        }

        /**
         * Retrieve Themosis class instance.
         *
         * @return \Themosis
         */
        public static function instance()
        {
            if (is_null(static::$instance)) {
                static::$instance = new static();
            }

            return static::$instance;
        }

        /**
         * Initialize the plugin.
         */
        protected function initialize()
        {
            // Check if there is a autoload.php file.
            // Meaning we're in development mode or
            // the plugin has been installed on a "classic" WordPress configuration.
            if (file_exists($autoload = __DIR__.DS.'vendor'.DS.'autoload.php')) {
                require $autoload;
            }
        }

        /**
         * Bootstrap the core plugin.
         */
        protected function bootstrap()
        {
            // Start the framework.
            if (file_exists($start = __DIR__.DS.'bootstrap'.DS.'start.php')) {
                require $start;
            }
        }
    }
}

/*
 * Globally register the instance.
 */
$GLOBALS['themosis'] = Themosis::instance();
