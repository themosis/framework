<?php

namespace Themosis\Foundation;

use League\Container\Container;

class Application extends Container
{
    /**
     * Project paths.
     * Same as $GLOBALS['themosis.paths'].
     *
     * @var array
     */
    protected $paths = [];

    public function __construct()
    {
        parent::__construct();

        $this->registerApplication();
    }

    /**
     * Register the Application class into the container,
     * so we can access it from the container itself.
     */
    public function registerApplication()
    {
        // Normally, only one instance is shared into the container.
        $this->add('app', $this);
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
            $this->add('path.'.$key, $path);
        }

        return $this;
    }

    /**
     * Dynamically access registered instances from the container.
     *
     * @param $name
     *
     * @return mixed|object
     */
    public function __get($name)
    {
        return $this->get($name);
    }
}
