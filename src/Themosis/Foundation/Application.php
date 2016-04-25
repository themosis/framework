<?php

namespace Themosis\Foundation;

use ArrayAccess;
use League\Container\Container;

class Application extends Container implements ArrayAccess
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
            $this->share('path.'.$key, $path);
        }

        return $this;
    }

    /**
     * Check if there is an instance registered into the container.
     *
     * @param string $name The instance alias.
     *
     * @return bool
     */
    public function offsetExists($name)
    {
        return $this->has($name);
    }

    /**
     * Return a registered instance from the service container.
     *
     * @param string $name The instance alias.
     *
     * @return mixed|object
     */
    public function offsetGet($name)
    {
        return $this->get($name);
    }

    /**
     * Add an instance to the service container.
     * 
     * @param string               $name   string The alias of the instance to register.
     * @param string|Closure|mixed $value The instance to add to the service container.
     */
    public function offsetSet($name, $value)
    {
        $this->add($name, $value);
    }

    /**
     * Remove an instance from the container.
     *
     * @param string $name The instance alias.
     */
    public function offsetUnset($name)
    {
        if (array_key_exists($name, $this->definitions)) {
            unset($this->definitions[$name]);
        }

        if (array_key_exists($name, $this->shared)) {
            unset($this->shared[$name]);
        }
    }

    /**
     * Dynamically access registered instances from the container.
     *
     * @param $name The instance alias used in the container.
     *
     * @return mixed|object
     */
    public function __get($name)
    {
        return $this[$name];
    }
}
