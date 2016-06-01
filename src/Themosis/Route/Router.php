<?php

namespace Themosis\Route;

use Illuminate\Events\Dispatcher;
use Illuminate\Routing\Router as IlluminateRouter;
use Themosis\Foundation\Application;

class Router extends IlluminateRouter
{
    /**
     * Build a Router instance.
     *
     * @param \Illuminate\Events\Dispatcher    $events
     * @param \Themosis\Foundation\Application $container
     */
    public function __construct(Dispatcher $events, Application $container)
    {
        parent::__construct($events, $container);
        $this->routes = new RouteCollection();
    }

    /**
     * Create a new Route object (Themosis Routing).
     *
     * @param array|string $methods
     * @param string       $uri
     * @param mixed        $action
     *
     * @return $this
     */
    protected function newRoute($methods, $uri, $action)
    {
        return (new Route($methods, $uri, $action))->setRouter($this)->setContainer($this->container);
    }
}
