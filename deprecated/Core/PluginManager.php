<?php

namespace Themosis\Core;

use Composer\Autoload\ClassLoader;
use Illuminate\Config\Repository;
use Themosis\Asset\Finder;
use Themosis\Core\Support\IncludesFiles;
use Themosis\Core\Support\PluginHeaders;
use Themosis\Core\Support\WordPressFileHeaders;

class PluginManager
{
    use WordPressFileHeaders, IncludesFiles, PluginHeaders;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var string
     */
    protected $filePath;

    /**
     * @var string
     */
    protected $dirPath;

    /**
     * @var string
     */
    protected $directory;

    /**
     * @var array
     */
    protected $parsedHeaders = [];

    /**
     * Configuration files namespace.
     *
     * @var string
     */
    protected $namespace;

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var ClassLoader
     */
    protected $loader;

    public function __construct(Application $app, string $filePath, ClassLoader $loader)
    {
        $this->app = $app;
        $this->filePath = $filePath;
        $this->dirPath = realpath(dirname($filePath));
        $this->loader = $loader;
        $this->config = $this->app->has('config') ? $this->app['config'] : new Repository();
    }

    /**
     * Load the plugin.
     *
     * @param string $configPath
     *
     * @return PluginManager
     */
    public function load(string $configPath): PluginManager
    {
        $this->setDirectory();
        $this->setConstants();
        $this->setPrefix();
        $this->loadPluginConfiguration($configPath);
        $this->setPluginAutoloading();

        return $this;
    }

    /**
     * Set the plugin directory name.
     */
    public function setDirectory()
    {
        $pos = strrpos($this->dirPath, DIRECTORY_SEPARATOR);
        $this->directory = substr($this->dirPath, $pos + 1);
    }

    /**
     * Return the plugin directory name.
     *
     * @return string
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }

    /**
     * Return the plugin root path.
     *
     * @param string $path Path to append to the plugin root path.
     *
     * @return string
     */
    public function getPath(string $path = ''): string
    {
        return $this->dirPath.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Return the plugin root URL.
     *
     * @param string $path
     *
     * @return string
     */
    public function getUrl(string $path = ''): string
    {
        return plugins_url($path, $this->filePath);
    }

    /**
     * Return a plugin header.
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
     * Return the plugin configuration files namespace.
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Return a plugin configuration value.
     *
     * @param string $key     Key configuration short name.
     * @param mixed  $default
     *
     * @return mixed
     */
    public function config(string $key, $default = null)
    {
        $fullnameKey = $this->getNamespace().'_'.$key;

        return $this->config->get($fullnameKey, $default);
    }

    /**
     * Register plugin assets directories.
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
     * Set plugin headers and plugin text domain constant.
     */
    protected function setConstants()
    {
        $this->parsedHeaders = $this->headers($this->filePath, $this->headers);

        $domainVar = $this->parsedHeaders['domain_var'] ?? 'PLUGIN_TD';
        $domainVar = strtoupper($domainVar);
        $textDomain = $this->parsedHeaders['text_domain'] ?? 'plugin-textdomain';

        if (! defined($domainVar)) {
            define($domainVar, $textDomain);
        }
    }

    /**
     * Set the plugin prefix.
     */
    protected function setPrefix()
    {
        $prefix = $this->parsedHeaders['plugin_prefix'] ?? 'tld_domain_plugin';

        $this->namespace = trim(str_replace(['-', '.', ' '], '_', $prefix));
    }

    /**
     * Load plugin configuration files.
     *
     * @param string $configPath
     */
    protected function loadPluginConfiguration(string $configPath)
    {
        $this->app->loadConfigurationFiles($this->config, $this->dirPath.'/'.trim($configPath, '\/'));
    }

    /**
     * Load plugin classes.
     */
    protected function setPluginAutoloading()
    {
        foreach ($this->config('plugin.autoloading', []) as $ns => $path) {
            $path = $this->dirPath.'/'.trim($path, '\/');
            $this->loader->addPsr4($ns, $path);
        }

        $this->loader->register();
    }

    /**
     * Register plugin services providers.
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
     * Register plugin views.
     *
     * @param array $paths
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

        foreach ($paths as $path) {
            $uri = $this->dirPath.'/'.trim($path, '\/');
            $factory->addLocation($uri);
            $twigLoader->addPath($uri);
        }

        return $this;
    }
}
