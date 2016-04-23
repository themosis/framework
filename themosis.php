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
//
// This constant is only used by the core plugin.
// Developers should not try to use it into their
// own projects.
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
     * @param string $name The path name/alias. If none is provided, returns all registered paths.
     *
     * @return string|array
     */
    function themosis_path($name = '')
    {
        if (!empty($name)) {
            return $GLOBALS['themosis.paths'][$name];
        }

        return $GLOBALS['themosis.paths'];
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

        /**
         * The service container.
         * 
         * @var \Themosis\Foundation\Application
         */
        public $app;

        private function __construct()
        {
            $this->autoload();
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
         * Check for the composer autoload file.
         */
        protected function autoload()
        {
            // Check if there is a autoload.php file.
            // Meaning we're in development mode or
            // the plugin has been installed on a "classic" WordPress configuration.
            if (file_exists($autoload = __DIR__.DS.'vendor'.DS.'autoload.php')) {
                require $autoload;

                // Developers using the framework in a "classic" WordPress
                // installation can activate this by defining
                // a THEMOSIS_ERROR constant and set its value to true or false
                // depending of their environment.
                if (defined('THEMOSIS_ERROR') && THEMOSIS_ERROR) {
                    $whoops = new \Whoops\Run();
                    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
                    $whoops->register();
                }
            }
        }

        /**
         * Bootstrap the core plugin.
         */
        protected function bootstrap()
        {
            /*
             * Define core framework paths.
             * These are real paths, not URLs to the framework files.
             */
            $paths['core'] = __DIR__.DS;
            $paths['sys'] = __DIR__.DS.'src'.DS.'Themosis'.DS;
            $paths['storage'] = THEMOSIS_STORAGE;
            themosis_set_paths($paths);

            /*
             * Instantiate the service container for the project.
             */
            $this->app = new \Themosis\Foundation\Application();

            /**
             * Setup the facade.
             */
            \Themosis\Facades\Facade::setFacadeApplication($this->app);

            /*
             * Project hooks.
             */
            add_action('plugins_loaded', [$this, 'pluginsLoaded'], 0);
            add_action('init', [$this, 'init'], 0);
        }

        /**
         * Used to register plugins configuration.
         * Services providers, ...
         */
        public function pluginsLoaded()
        {
            /**
             * Service providers.
             */
            $providers = apply_filters('themosis_service_providers', [
                'Themosis\Action\ActionServiceProvider',
                //'Themosis\Ajax\AjaxServiceProvider',
                'Themosis\Asset\AssetServiceProvider',
                /*'Themosis\Config\ConfigServiceProvider',
                'Themosis\Field\FieldServiceProvider',
                'Themosis\Html\HtmlServiceProvider',
                'Themosis\Metabox\MetaboxServiceProvider',
                'Themosis\Page\PageServiceProvider',
                'Themosis\PostType\PostTypeServiceProvider',
                'Themosis\Route\RouteServiceProvider',
                'Themosis\Taxonomy\TaxonomyServiceProvider',
                'Themosis\User\UserServiceProvider',
                'Themosis\Validation\ValidationServiceProvider',
                'Themosis\View\ViewServiceProvider'*/
            ]);

            foreach ($providers as $provider) {
                $this->app->addServiceProvider($provider);
            }
        }

        /**
         * Initialize the project.
         */
        public function init()
        {
            /*
             * Register into the container, the registered paths.
             * Normally at this stage, plugins and theme should have
             * their paths registered into the $GLOBALS array.
             */
            $this->app->registerAllPaths(themosis_path());
        }
    }
}

/*
 * Globally register the instance.
 */
$GLOBALS['themosis'] = Themosis::instance();
