<?php

namespace Themosis\Core;

use Composer\Autoload\ClassLoader;
use Illuminate\Config\Repository;
use Themosis\Core\Support\WordPressFileHeaders;
use Themosis\Core\Theme\ImageSize;
use Themosis\Core\Theme\Support;
use Themosis\Core\Theme\Templates;

class ThemeManager
{
    use WordPressFileHeaders;

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
        'text_domain' => 'Text Domain'
    ];

    /**
     * @var ImageSize
     */
    protected $images;

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
        $this->setThemeConstants();
        $this->loadThemeConfiguration($path);
        $this->setThemeAutoloading();
        $this->registerThemeServicesProviders();
        $this->setThemeViews();
        $this->setThemeImages();
        $this->setThemeMenus();
        $this->setThemeSidebars();
        $this->setThemeSupport();
        $this->setThemeTemplates();

        return $this;
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
     */
    protected function registerThemeServicesProviders()
    {
        $providers = $this->config->get('theme.providers', []);

        foreach ($providers as $provider) {
            $this->app->register(new $provider($this->app));
        }
    }

    /**
     * Register theme views path.
     */
    protected function setThemeViews()
    {
        if (! $this->app->has('view')) {
            return;
        }

        $paths = $this->config->get('theme.views', []);

        if (empty($paths)) {
            return;
        }

        $factory = $this->app->make('view');

        foreach ($paths as $path) {
            $dir = trim($path, '\/');
            $factory->addLocation($this->dirPath.'/'.$dir);
        }
    }

    /**
     * Register theme constants.
     */
    protected function setThemeConstants()
    {
        $headers = $this->headers($this->dirPath.'/style.css', $this->headers);

        // Theme text domain.
        $textdomain = $headers['text_domain'] ?? 'themosis_theme';
        defined('THEME_TD') ? THEME_TD : define('THEME_TD', $textdomain);
    }

    /**
     * Register theme image sizes.
     */
    protected function setThemeImages()
    {
        $this->images = (new ImageSize($this->config->get('images'), $this->app['filter']))
            ->register();
    }

    /**
     * Return the theme images sizes.
     *
     * @return ImageSize
     */
    public function images()
    {
        return $this->images;
    }

    /**
     * Register theme menus locations.
     */
    protected function setThemeMenus()
    {
        if (function_exists('register_nav_menus') && $this->config->has('menus')) {
            register_nav_menus($this->config->get('menus'));
        }
    }

    /**
     * Register theme sidebars.
     */
    protected function setThemeSidebars()
    {
        if (! function_exists('register_sidebar')) {
            return;
        }

        $sidebars = $this->config->get('sidebars', []);

        if (! empty($sidebars)) {
            foreach ($sidebars as $sidebar) {
                register_sidebar($sidebar);
            }
        }
    }

    /**
     * Register theme support features.
     */
    protected function setThemeSupport()
    {
        (new Support($this->config->get('support', [])))->register();
    }

    /**
     * Register theme templates.
     */
    protected function setThemeTemplates()
    {
        (new Templates($this->config->get('templates', []), $this->app['filter']))
            ->register();
    }
}
