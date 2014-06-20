<?php
namespace Themosis\Route;

use Themosis\Core\Container;
use Themosis\Core\Request;

class ControllerDispatcher {

    /**
     * Route filterer...
     *
     * @var Router
     */
    protected $filterer;

    /**
     * IoC container.
     *
     * @var \Themosis\Core\Container
     */
    protected $container;

    /**
     * Build a ControllerDispatcher instance.
     *
     * @param Router $router
     * @param Container $container
     */
    public function __construct(Router $router, Container $container)
    {
        $this->filterer = $router;
        $this->container = $container;
    }

    /**
     * Dispatch a request to a given controller and method.
     *
     * @param \Themosis\Route\Route $route
     * @param \Themosis\Core\Request $request
     * @param string $controller The controller class name.
     * @param string $method The controller class method to call.
     * @return mixed
     */
    public function dispatch(Route $route, Request $request, $controller, $method)
    {
        // Get an instance of the controller from the IoC container.
        $instance = $this->makeController($controller);

        $response = $this->call($instance, $route, $method);

        return $response;
    }

    /**
     * Make a controller instance via the IoC container.
     *
     * @param string $controller The controller class name.
     * @return mixed
     */
    protected function makeController($controller)
    {
        return $this->container->make($controller);
    }

    /**
     * Call the given controller instance method.
     *
     * @param  \Themosis\Route\Controller $instance
     * @param  \Themosis\Route\Route $route
     * @param  string $method
     * @return mixed
     */
    protected function call($instance, $route, $method)
    {
        $parameters = $route->parametersWithoutNulls();

        return $instance->callAction($method, $parameters);
    }

    /**
     * Return the IoC.
     *
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

} 