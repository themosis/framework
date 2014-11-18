<?php
/*
Plugin Name: Themosis framework
Plugin URI: http://framework.themosis.com/
Description: A framework for WordPress developers.
Version: 1.0.7
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
        private static $instance = null;

        /**
         * Framework version.
         *
         * @var float
         */
        const VERSION = '1.0.7';

        /**
         * Plugin directory name.
         *
         * @var string
         */
        private static $dirName = '';

        private function __construct()
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
                <p><?php _e(sprintf('<b>Themosis plugin:</b> %s', "Symfony Class Loader component not found. Make sure to include it before proceeding."), THEMOSIS_FRAMEWORK_TEXTDOMAIN); ?></p>
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
            // Default path to Composer autoload file.
            $autoload = __DIR__.DS.'vendor'.DS.'autoload.php';

            if (defined('THEMOSIS_AUTOLOAD'))
            {
                if (!THEMOSIS_AUTOLOAD && file_exists($autoload))
                {
                    require_once($autoload);
                }
            }
            elseif (!defined('THEMOSIS_AUTOLOAD'))
            {
                if (file_exists($autoload))
                {
                    require_once($autoload);
                }
            }

            if (!class_exists('Symfony\Component\ClassLoader\ClassLoader'))
            {
                add_action('admin_notices', array($this, 'displayMessage'));
                return;
            }

            // Autoload PSR-0 classes.
            // The autoloading process is not handled by Composer...
            // This mechanism allows a developer to use dependencies inside the plugin
            // or to use them at the root of the WordPress project.
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
            // Framework paths.
            $paths = apply_filters('themosis_framework_paths', array());

            // Plugin base path.
            $paths['plugin'] = realpath(__DIR__).DS;

            // Framework base path.
            $paths['sys'] = realpath(__DIR__).DS.'src'.DS.'Themosis'.DS;

            // Register globally the paths
            foreach ($paths as $name => $path)
            {
               if (!isset($GLOBALS['themosis_paths'][$name]))
               {
                   $GLOBALS['themosis_paths'][$name] = $path;
               }
            }

            // Bootstrap the framework
            if (isset($GLOBALS['THFWK_Themosis']))
            {
                require_once themosis_path('plugin').'bootstrap'.DS.'start.php';
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
}

/**
 * Load the main class.
 *
 */
add_action('plugins_loaded', function(){

	$GLOBALS['THFWK_Themosis'] = THFWK_Themosis::getInstance();

});