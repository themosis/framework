<?php
namespace Themosis\Route;

use Themosis\Core\Container;

class Router {

    /**
     * The framework IoC.
     *
     * @var \Themosis\Core\Container
     */
    protected $container;

    /**
     * The RouteCollection instance.
     *
     * @var RouteCollection
     */
    protected $routes;

    /**
     * Build a Router instance.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->routes = new RouteCollection();
    }

} 