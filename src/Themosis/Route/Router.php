<?php
namespace Themosis\Route;

use Closure;
use Themosis\Core\Container;
use Themosis\Core\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

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
     * @param string $condition A WordPress conditional tag.
     * @param \Closure|array|string $action
     * @return \Themosis\Route\Route
     */
    public function get($condition, $action)
    {
        return $this->addRoute(['GET', 'HEAD'], $condition, $action);
    }

    /**
     * Register a new POST route with the router.
     *
     * @param string $condition A WordPress conditional tag.
     * @param \Closure|array|string $action
     * @return \Themosis\Route\Route
     */
    public function post($condition, $action)
    {
        return $this->addRoute('POST', $condition, $action);
    }

    /**
     * Register a new route responding to all verbs.
     *
     * @param string $condition
     * @param \Closure|array|string $action
     * @return \Themosis\Route\Route
     */
    public function any($condition, $action)
    {
        $verbs = ['GET', 'HEAD', 'POST'];
        return $this->addRoute($verbs, $condition, $action);
    }

    /**
     * Register a new route with the given verbs.
     *
     * @param array|string $methods
     * @param string $condition
     * @param \Closure|array|string  $action
     * @return \Themosis\Route\Route
     */
    public function match($methods, $condition, $action)
    {
        return $this->addRoute($methods, $condition, $action);
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
        if($this->routingToController($action))
        {
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
        if(is_string($action))
        {
            $action = ['uses' => $action];
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

        return function() use($d, $controller)
        {
            $ioc = $d->getContainer();
            $router = $ioc['router'];
            $route = $router->current();
            $request = $router->getCurrentRequest();

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
        if(is_null($this->controllerDispatcher))
        {
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

    /**
     * Dispatch the request to the application.
     *
     * @param \Themosis\Core\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dispatch(Request $request)
    {
        $this->currentRequest = $request;
        $response = $this->dispatchToRoute($request);
        $response = $this->prepareResponse($request, $response);
        return $response;
    }

    /**
     * Dispatch the request to a route and return the response.
     *
     * @param \Themosis\Core\Request $request
     * @return mixed
     */
    public function dispatchToRoute(Request $request)
    {
        $route = $this->findRoute($request);

        //@todo implement route before actions

        // Check if a route exists for the request.
        if(!is_null($route))
        {
            $response = $route->run();
        }
        else
        {
            $view = $this->container['view'];
            $response = $view->make('_themosisNoRoute');
        }

        $response = $this->prepareResponse($request, $response);

        return $response;
    }

    /**
     * Find the route matching a given request.
     *
     * @param  \Themosis\Core\Request $request
     * @return \Themosis\Route\Route
     */
    protected function findRoute($request)
    {
        $this->current = $route = $this->routes->match($request);
        return $route;
    }

    /**
     * Create a response instance from the given value.
     *
     * @param \Themosis\Core\Request $request
     * @param mixed $response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function prepareResponse($request, $response)
    {
        if(!$response instanceof SymfonyResponse)
        {
            $response = new SymfonyResponse($response);
        }

        return $response->prepare($request);
    }
} 