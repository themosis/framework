<?php

namespace Themosis\Route;

use Illuminate\Routing\Router as IlluminateRouter;

class Router extends IlluminateRouter
{
    /**
     * Create a new Route object.
     *
     * @param array|string $methods
     * @param string       $uri
     * @param mixed        $action
     *
     * @return \Illuminate\Routing\Route
     */
    protected function newRoute($methods, $uri, $action)
    {
        $config = $this->container->has('config') ? $this->container->make('config') : null;

        return (new Route($methods, $uri, $action))
            ->setRouter($this)
            ->setContainer($this->container)
            ->setConditions(! is_null($config) ? $config->get('app.conditions', []) : []);
    }
}
