<?php

namespace Themosis\Foundation\Theme;

use Composer\Autoload\ClassLoader;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Themosis\Asset\Finder;
use Themosis\Foundation\Features\ImageSize;
use Themosis\Foundation\Features\Support;
use Themosis\Foundation\Features\Templates;
use Themosis\Foundation\Support\HasConfigurationFiles;
use Themosis\Foundation\Support\IncludesFiles;
use Themosis\Foundation\Support\WordPressFileHeaders;
use Themosis\Hook\Filter;

class Manager
{
    use HasConfigurationFiles;
    use IncludesFiles;
    use WordPressFileHeaders;

    protected string $path;

    protected string $directory;

    protected array $parsedHeaders = [];

    public ImageSize $images;

    public array $headers = [
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

    public function __construct(
        protected Application $app,
        protected ClassLoader $loader,
        protected Repository  $config,
        protected Filter $filter,
    ) {
    }

    /**
     * Load the theme.
     */
    public function load(string $path, string $configDirectory = 'config'): self
    {
        $this->setPath($path);
        $this->setHeaders();
        $this->setThemeDirectory();
        $this->setThemeConstants();
        $this->loadThemeConfiguration($configDirectory);
        $this->setThemeAutoload();

        return $this;
    }

    /**
     * Return a theme header property.
     */
    public function getHeader(string $header): ?string
    {
        return $this->parsedHeaders[$header] ?? null;
    }

    protected function setHeaders(): void
    {
        $this->parsedHeaders = $this->headers($this->path . '/style.css', $this->headers);
    }

    /**
     * Return the theme directory name.
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }

    protected function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * Return the theme root path.
     */
    public function getPath(string $path = ''): string
    {
        return $this->path . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Return the theme root URL.
     */
    public function getUrl(string $uri = ''): string
    {
        if (is_multisite() && defined(SUBDOMAIN_INSTALL) && SUBDOMAIN_INSTALL) {
            return sprintf(
                '%s/%s/themes/%s',
                get_home_url(),
                CONTENT_DIR,
                $this->getDirectory(),
            ) . ($uri ? '/' . $uri : $uri);
        }

        return get_template_directory_uri() . ($uri ? '/' . $uri : $uri);
    }

    /**
     * Set the theme directory name property.
     */
    protected function setThemeDirectory(): void
    {
        $pos = strrpos($this->path, DIRECTORY_SEPARATOR);

        $this->directory = substr($this->path, $pos + 1);
    }

    /**
     * Load theme configuration files.
     */
    protected function loadThemeConfiguration(string $configDirectory): void
    {
        $files = $this->getConfigurationFiles($this->path . '/' . trim($configDirectory, '\/'));

        foreach ($files as $key => $path) {
            $this->config->set($key, require $path);
        }
    }

    /**
     * Load theme classes.
     */
    protected function setThemeAutoload(): void
    {
        foreach ($this->config->get('theme.autoload', []) as $ns => $path) {
            $path = $this->path . '/' . trim($path, '\/');
            $this->loader->addPsr4($ns, $path);
        }

        $this->loader->register();
    }

    /**
     * Register theme services providers.
     */
    public function providers(array $providers = []): self
    {
        foreach ($providers as $provider) {
            $this->app->register(new $provider($this->app));
        }

        return $this;
    }

    /**
     * Define theme assets directories.
     */
    public function assets(array $locations): self
    {
        $finder = $this->app->bound(Finder::class) ? $this->app[Finder::class] : null;

        if (! is_null($finder)) {
            /** @var Finder $finder */
            $finder->addLocations($locations);
        }

        return $this;
    }

    /**
     * Register theme views path.
     */
    public function views(array $paths = []): self
    {
        if (! $this->app->has('view')) {
            return $this;
        }

        if (empty($paths)) {
            return $this;
        }

        $factory = $this->app->make('view');
        $twigLoader = $this->app->has('twig.loader')
            ? $this->app->make('twig.loader')
            : null;

        foreach ($paths as $path) {
            $uri = $this->path . '/' . trim($path, '\/');

            $factory->getFinder()->prependLocation($uri);
            $twigLoader?->prependPath($uri);
        }

        return $this;
    }

    /**
     * Register theme constants.
     */
    protected function setThemeConstants(): void
    {
        $textdomain = $this->parsedHeaders['text_domain'] ?? 'themosis_theme';

        defined('THEME_TD') ?: define('THEME_TD', $textdomain);
    }

    /**
     * Register theme image sizes.
     */
    public function images(array $sizes = []): self
    {
        if (empty($sizes)) {
            return $this;
        }

        $this->images = (new ImageSize($sizes, $this->filter))
            ->register();

        return $this;
    }

    /**
     * Return a configuration value.
     */
    public function config(string $key, mixed $default = null): mixed
    {
        return $this->config->get($key, $default);
    }

    public function getImages(): ImageSize
    {
        return $this->images;
    }

    /**
     * Register theme menus locations.
     */
    public function menus(array $menus = []): self
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
     */
    public function sidebars(array $sidebars = []): self
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
     */
    public function support(array $features = []): self
    {
        (new Support($features))->register();

        return $this;
    }

    /**
     * Register theme templates.
     */
    public function templates(array $templates = []): self
    {
        (new Templates($templates, $this->filter))
            ->register();

        return $this;
    }
}
