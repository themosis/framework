<?php
namespace Themosis\Route;

use Closure;
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
     * The current dispatched route.
     *
     * @var \Themosis\Route\Route
     */
    protected $current;

    /**
     * The current request instance.
     *
     * @var \Themosis\Core\Request
     */
    protected $currentRequest;

    /**
     * The controller dispatcher instance.
     *
     * @var \Themosis\Route\ControllerDispatcher
     */
    protected $controllerDispatcher;

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

    /**
     * Register a route listening to GET requests.
     *
     * @param string $condition A WordPress template condition.
     * @param \Closure|array|string $action
     * @return \Themosis\Route\Route
     */
    public function get($condition, $action)
    {
        return $this->addRoute(array('GET', 'HEAD'), $condition, $action);
    }

    /**
     * Add a route to route collection.
     *
     * @param array|string $methods Http methods.
     * @param string $condition
     * @param \Closure|array|string $action
     * @return \Themosis\Route\Route
     */
    protected function addRoute($methods, $condition, $action)
    {
        return $this->routes->add($this->createRoute($methods, $condition, $action));
    }

    /**
     * Create a new route instance.
     *
     * @param array|string $methods
     * @param string $condition
     * @param mixed $action
     * @return \Themosis\Route\Route
     */
    protected function createRoute($methods, $condition, $action)
    {
        // Check if we're using a controller and defined its
        // $action closure.
        if($this->routingToController($action)){

            // $action = array('uses' => $closure, 'controller' => 'SomeController@oneMethod')
            $action = $this->getControllerAction($action);

        }

        return new Route($methods, $condition, $action);
    }

    /**
     * Determine if the action is routing to a controller.
     *
     * @param array $action
     * @return bool
     */
    protected function routingToController($action)
    {
        if($action instanceof Closure) return false;

        return is_string($action) || is_string(array_get($action, 'uses'));
    }

    /**
     * Add a controller based route action to the action array.
     *
     * @param array|string $action
     * @return array
     */
    protected function getControllerAction($action)
    {
        if(is_string($action)){

            $action = array('uses' => $action);

        }

        $action['controller'] = $action['uses'];

        $closure = $this->getClassClosure($action['uses']);

        return array_set($action, 'uses', $closure);
    }

    /**
     * Get the Closure for a controller based action.
     *
     * @param string $controller
     * @return \Closure
     */
    protected function getClassClosure($controller)
    {
        $d = $this->getControllerDispatcher();
        $r = $this;

        return function() use($d, $controller, $r){

            $route = $r->current();
            $request = $r->getCurrentRequest();

            // Now we can split the controller and method out of the action string so that we
            // can call them appropriately on the class. This controller and method are in
            // in the Class@method format and we need to explode them out then use them.
            list($class, $method) = explode('@', $controller);

            return $d->dispatch($route, $request, $class, $method);
        };
    }

    /**
     * Get the controller dispatcher instance.
     *
     * @return \Themosis\Route\ControllerDispatcher
     */
    public function getControllerDispatcher()
    {
        if(is_null($this->controllerDispatcher)){

            $this->controllerDispatcher = new ControllerDispatcher($this, $this->container);

        }

        return $this->controllerDispatcher;
    }

    /**
     * Get the currently dispatched route instance.
     *
     * @return \Themosis\Route\Route
     */
    public function current()
    {
        return $this->current;
    }

    /**
     * Get the request currently being dispatched.
     *
     * @return \Themosis\Core\Request
     */
    public function getCurrentRequest()
    {
        return $this->currentRequest;
    }

} 