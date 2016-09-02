<?php

namespace Themosis\Foundation;

use Illuminate\Container\Container;

class Application extends Container
{
    /**
     * Project paths.
     * Same as $GLOBALS['themosis.paths'].
     *
     * @var array
     */
    protected $paths = [];

    /**
     * The loaded service providers.
     *
     * @var array
     */
    protected $loadedProviders = [];

    public function __construct()
    {
        $this->registerApplication();
    }

    /**
     * Register the Application class into the container,
     * so we can access it from the container itself.
     */
    public function registerApplication()
    {
        // Normally, only one instance is shared into the container.
        static::setInstance($this);
        $this->instance('app', $this);
    }

    /**
     * Register into the application instance, all project
     * paths registered.
     * Setup this method to be called later on an 'init' hook only.
     *
     * @param array $paths The registered paths.
     *
     * @return \Themosis\Foundation\Application
     */
    public function registerAllPaths(array $paths)
    {
        $this->paths = $paths;

        foreach ($paths as $key => $path) {
            $this->instance('path.'.$key, $path);
        }

        return $this;
    }

    /**
     * Register a service provider with the application.
     *
     * @param \Themosis\Foundation\ServiceProvider|string $provider
     * @param array                                       $options
     * @param bool                                        $force
     *
     * @return \Themosis\Foundation\ServiceProvider
     */
    public function register($provider, array $options = [], $force = false)
    {
        if (!$provider instanceof ServiceProvider) {
            $provider = new $provider($this);
        }
        if (array_key_exists($providerName = get_class($provider), $this->loadedProviders)) {
            return;
        }
        $this->loadedProviders[$providerName] = true;
        $provider->register();

        if (method_exists($provider, 'boot')) {
            $provider->boot();
        }
    }
}
