<?php
/*
Plugin Name: Themosis framework
Plugin URI: https://framework.themosis.com/
Description: A WordPress framework.
Version: 1.3.2
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
    class Themosis
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
        const VERSION = '1.3.2';

        /**
         * The service container.
         *
         * @var \Themosis\Foundation\Application
         */
        public $container;

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
            $this->container = new \Themosis\Foundation\Application();

            /*
             * Create a new Request instance and register it.
             * By providing an instance, the instance is shared.
             */
            $request = \Themosis\Foundation\Request::capture();
            $this->container->instance('request', $request);

            /*
             * Setup the facade.
             */
            \Themosis\Facades\Facade::setFacadeApplication($this->container);

            /*
             * Register into the container, the registered paths.
             * Normally at this stage, plugins should have
             * their paths registered into the $GLOBALS array.
             */
            $this->container->registerAllPaths(themosis_path());

            /*
             * Register core service providers.
             */
            $this->registerProviders();

            /*
             * Setup core.
             */
            $this->setup();

            /*
             * Project hooks.
             * Added in their called order.
             */
            add_action('admin_enqueue_scripts', [$this, 'adminEnqueueScripts']);
            add_action('admin_head', [$this, 'adminHead']);
            add_action('template_redirect', 'redirect_canonical');
            add_action('template_redirect', 'wp_redirect_admin_locations');
            add_action('template_redirect', [$this, 'setRouter'], 20);
        }

        /**
         * Register core framework service providers.
         */
        protected function registerProviders()
        {
            /*
             * Service providers.
             */
            $providers = apply_filters('themosis_service_providers', [
                Themosis\Ajax\AjaxServiceProvider::class,
                Themosis\Asset\AssetServiceProvider::class,
                Themosis\Config\ConfigServiceProvider::class,
                Themosis\Database\DatabaseServiceProvider::class,
                Themosis\Field\FieldServiceProvider::class,
                Themosis\Finder\FinderServiceProvider::class,
                Themosis\Hook\HookServiceProvider::class,
                Themosis\Html\FormServiceProvider::class,
                Themosis\Html\HtmlServiceProvider::class,
                Themosis\Load\LoaderServiceProvider::class,
                Themosis\Metabox\MetaboxServiceProvider::class,
                Themosis\Page\PageServiceProvider::class,
                Themosis\Page\Sections\SectionServiceProvider::class,
                Themosis\PostType\PostTypeServiceProvider::class,
                Themosis\Route\RouteServiceProvider::class,
                Themosis\Taxonomy\TaxonomyServiceProvider::class,
                Themosis\User\UserServiceProvider::class,
                Themosis\Validation\ValidationServiceProvider::class,
                Themosis\View\ViewServiceProvider::class,
            ]);

            foreach ($providers as $provider) {
                $this->container->register($provider);
            }
        }

        /**
         * Setup core framework parameters.
         * At this moment, all activated plugins have been loaded.
         * Each plugin has its service providers registered.
         */
        protected function setup()
        {
            /*
             * Add view paths.
             */
            $viewFinder = $this->container['view.finder'];
            $viewFinder->addLocation(themosis_path('sys').'Metabox'.DS.'Views');
            $viewFinder->addLocation(themosis_path('sys').'Page'.DS.'Views');
            $viewFinder->addLocation(themosis_path('sys').'PostType'.DS.'Views');
            $viewFinder->addLocation(themosis_path('sys').'Field'.DS.'Fields'.DS.'Views');
            $viewFinder->addLocation(themosis_path('sys').'Taxonomy'.DS.'Views');
            $viewFinder->addLocation(themosis_path('sys').'User'.DS.'Views');

            /*
             * Add paths to asset finder.
             */
            $url = plugins_url('src/Themosis/_assets', __FILE__);
            $assetFinder = $this->container['asset.finder'];
            $assetFinder->addPaths([$url => themosis_path('sys').'_assets']);

            /*
             * Add framework core assets URL to the global
             * admin JS object.
             */
            add_filter('themosisAdminGlobalObject', function ($data) use ($url) {
                $data['_themosisAssets'] = $url;

                return $data;
            });

            /*
             * Register framework media image size.
             */
            $images = new Themosis\Config\Images([
                '_themosis_media' => [100, 100, true, __('Mini', THEMOSIS_FRAMEWORK_TEXTDOMAIN)],
            ], $this->container['filter']);
            $images->make();

            /*
             * Register framework assets.
             */
            $this->container['asset']->add('themosis-core-styles', 'css/_themosisCore.css', ['wp-color-picker'])->to('admin');
            $this->container['asset']->add('themosis-core-scripts', 'js/_themosisCore.js', ['jquery', 'jquery-ui-sortable', 'underscore', 'backbone', 'mce-view', 'wp-color-picker'], '1.3.0', true)->to('admin');
        }

        /**
         * Hook into front-end routing.
         * Setup the router API to be executed before
         * theme default templates.
         */
        public function setRouter()
        {
            if (is_feed() || is_comment_feed()) {
                return;
            }

            try {
                $request = $this->container['request'];
                $response = $this->container['router']->dispatch($request);

                // We only send back the content because, headers are already defined
                // by WordPress internals.
                $response->sendContent();
            } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $exception) {
                /*
                 * Fallback to WordPress templates.
                 */
            }
        }

        /**
         * Enqueue Admin scripts.
         */
        public function adminEnqueueScripts()
        {
            /*
             * Make sure the media scripts are always enqueued.
             */
            wp_enqueue_media();
        }

        /**
         * Output a global JS object in the <head> tag for the admin.
         * Allow developers to add JS data for their project in the admin area only.
         */
        public function adminHead()
        {
            $datas = apply_filters('themosisAdminGlobalObject', []);

            $output = "<script type=\"text/javascript\">\n\r";
            $output .= "//<![CDATA[\n\r";
            $output .= "var themosisAdmin = {\n\r";

            if (!empty($datas)) {
                foreach ($datas as $key => $value) {
                    $output .= $key.': '.json_encode($value).",\n\r";
                }
            }

            $output .= "};\n\r";
            $output .= "//]]>\n\r";
            $output .= '</script>';

            // Output the datas.
            echo $output;
        }
    }
}

/*
 * Globally register the instance.
 */
$GLOBALS['themosis'] = Themosis::instance();
