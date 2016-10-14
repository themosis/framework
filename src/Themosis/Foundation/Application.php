<?php

namespace Themosis\Foundation;

use Illuminate\Foundation\Application as BaseApplication;

class Application extends BaseApplication
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

    public function __construct($paths = null)
    {
        parent::__construct($paths);
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
            $this->instance('path.' . $key, $path);
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

}
