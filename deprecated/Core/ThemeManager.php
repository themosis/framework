<?php

namespace Themosis\Core;

use Composer\Autoload\ClassLoader;
use Illuminate\Config\Repository;
use Themosis\Asset\Finder;
use Themosis\Core\Support\IncludesFiles;
use Themosis\Core\Support\WordPressFileHeaders;
use Themosis\Core\Theme\ImageSize;
use Themosis\Core\Theme\Support;
use Themosis\Core\Theme\Templates;

class ThemeManager
{
    use WordPressFileHeaders, IncludesFiles;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var ClassLoader
     */
    protected $loader;

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var string
     */
    protected $dirPath;

    /**
     * @var \WP_Theme
     */
    protected $theme;

    /**
     * @var string
     */
    protected $routesPath;

    /**
     * @var array
     */
    public $headers = [
        'name' => 'Theme Name',
        'theme_uri' => 'Theme URI',
        'author' => 'Author',
        'author_uri' => 'Author URI',
        'description' => 'Description',
        'version' => 'Version',
        'license' => 'License',
        'license_uri' => 'License URI',
        'text_domain' => 'Text Domain',
        'domain_path' => 'Domain Path',
    ];

    /**
     * @var array
     */
    protected $parsedHeaders = [];

    /**
     * @var ImageSize
     */
    public $images;

    /**
     * The theme directory name.
     *
     * @var string
     */
    protected $directory;

    public function __construct(Application $app, string $dirPath, ClassLoader $loader)
    {
        $this->app = $app;
        $this->dirPath = $dirPath;
        $this->loader = $loader;
        $this->config = $this->app->has('config') ? $this->app['config'] : new Repository();
    }

    /**
     * Load the theme. Setup theme requirements.
     *
     * @param string $path Theme configuration folder path.
     *
     * @return $this
     */
    public function load(string $path): ThemeManager
    {
        $this->setThemeDirectory();
        $this->setThemeConstants();
        $this->loadThemeConfiguration($path);
        $this->setThemeAutoloading();

        return $this;
    }

    /**
     * Define theme assets directories.
     *
     * @param array $locations
     *
     * @return $this
     */
    public function assets(array $locations)
    {
        $finder = $this->app->bound('asset.finder') ? $this->app['asset.finder'] : null;

        if (! is_null($finder)) {
            /** @var Finder $finder */
            $finder->addLocations($locations);
        }

        return $this;
    }

    /**
     * Return a theme header property.
     *
     * @param string $header
     *
     * @return string|null
     */
    public function getHeader(string $header)
    {
        return $this->parsedHeaders[$header] ?? null;
    }

    /**
     * Return the theme directory name.
     *
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * Return the theme root path.
     *
     * @param string $path Path to append to the theme base path.
     *
     * @return string
     */
    public function getPath(string $path = '')
    {
        return $this->dirPath.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Return the theme root URL.
     *
     * @param string $path Path to append to the theme base URL.
     *
     * @return string
     */
    public function getUrl(string $path = '')
    {
        if (is_multisite() && defined(SUBDOMAIN_INSTALL) && SUBDOMAIN_INSTALL) {
            return sprintf(
                '%s/%s/themes/%s',
                get_home_url(),
                CONTENT_DIR,
                $this->getDirectory()
            ).($path ? '/'.$path : $path);
        }

        return get_template_directory_uri().($path ? '/'.$path : $path);
    }

    /**
     * Set the theme directory name property.
     */
    protected function setThemeDirectory()
    {
        $pos = strrpos($this->dirPath, DIRECTORY_SEPARATOR);

        $this->directory = substr($this->dirPath, $pos + 1);
    }

    /**
     * Load theme configuration files.
     *
     * @param string $path
     */
    protected function loadThemeConfiguration(string $path)
    {
        $this->app->loadConfigurationFiles($this->config, $path);
    }

    /**
     * Load theme classes.
     */
    protected function setThemeAutoloading()
    {
        foreach ($this->config->get('theme.autoloading', []) as $ns => $path) {
            $path = $this->dirPath.'/'.trim($path, '\/');
            $this->loader->addPsr4($ns, $path);
        }

        $this->loader->register();
    }

    /**
     * Register theme services providers.
     *
     * @param array $providers
     *
     * @return $this
     */
    public function providers(array $providers = [])
    {
        foreach ($providers as $provider) {
            $this->app->register(new $provider($this->app));
        }

        return $this;
    }

    /**
     * Register theme views path.
     *
     * @param array $paths
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return $this
     */
    public function views(array $paths = [])
    {
        if (! $this->app->has('view')) {
            return $this;
        }

        if (empty($paths)) {
            return $this;
        }

        $factory = $this->app->make('view');
        $twigLoader = $this->app->make('twig.loader');

        foreach ($paths as $path => $priority) {
            if (is_numeric($path)) {
                $location = $priority;

                // Set a default base priority for themes
                // that do not define one.
                $priority = 100;
            } else {
                $location = $path;
            }

            $uri = $this->dirPath.'/'.trim($location, '\/');
            $factory->getFinder()->addOrderedLocation($uri, $priority);
            $twigLoader->addPath($uri);
        }

        return $this;
    }

    /**
     * Register theme constants.
     */
    protected function setThemeConstants()
    {
        $this->parsedHeaders = $this->headers($this->dirPath.'/style.css', $this->headers);

        // Theme text domain.
        $textdomain = (isset($this->parsedHeaders['text_domain']) && ! empty($this->parsedHeaders['text_domain']))
            ? $this->parsedHeaders['text_domain']
            : 'themosis_theme';

        defined('THEME_TD') ? THEME_TD : define('THEME_TD', $textdomain);
    }

    /**
     * Register theme image sizes.
     *
     * @param array $sizes
     *
     * @return $this
     */
    public function images(array $sizes = [])
    {
        if (empty($sizes)) {
            return $this;
        }

        $this->images = (new ImageSize($sizes, $this->app['filter']))
            ->register();

        return $this;
    }

    /**
     * Return a configuration value.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function config(string $key, $default = null)
    {
        return $this->config->get($key, $default);
    }

    /**
     * Return the theme images sizes.
     *
     * @return ImageSize
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Register theme menus locations.
     *
     * @param array $menus
     *
     * @return $this
     */
    public function menus(array $menus = [])
    {
        if (empty($menus)) {
            return $this;
        }

        if (function_exists('register_nav_menus')) {
            register_nav_menus($menus);
        }

        return $this;
    }

    /**
     * Register theme sidebars.
     *
     * @param array $sidebars
     *
     * @return $this
     */
    public function sidebars($sidebars = [])
    {
        if (empty($sidebars)) {
            return $this;
        }

        if (function_exists('register_sidebar')) {
            foreach ($sidebars as $sidebar) {
                register_sidebar($sidebar);
            }
        }

        return $this;
    }

    /**
     * Register theme support features.
     *
     * @param array $features
     *
     * @return $this
     */
    public function support($features = [])
    {
        (new Support($features))->register();

        return $this;
    }

    /**
     * Register theme templates.
     *
     * @param array $templates
     *
     * @return $this
     */
    public function templates($templates = [])
    {
        (new Templates($templates, $this->app['filter']))
            ->register();

        return $this;
    }
}
