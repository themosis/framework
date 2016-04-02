<?php
namespace Themosis\Route;

use Closure;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Illuminate\Container\Container as IlluminateContainer;
use Illuminate\Events\Dispatcher as IlluminateDispatcher;
use Illuminate\Http\Request as IlluminateRequest;
use Illuminate\Routing\Router as IlluminateRouter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Router extends IlluminateRouter
{

    public function __construct(IlluminateContainer $container)
    {
        $events = new IlluminateDispatcher(new IlluminateContainer());

        parent::__construct($events, $container);

        $this->routes = new RouteCollection;
    }

    /**
     * Create a new Route object.
     *
     * @param  array|string $methods
     * @param  string $uri
     * @param  mixed $action
     * @return \Themosis\Route\Route
     */
    protected function newRoute($methods, $uri, $action)
    {
        return new Route($methods, $uri, $action);
    }

    /**
     * Dispatch the request to the application.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function dispatch(IlluminateRequest $request)
    {
        try {
            return parent::dispatch($request);
        } catch (NotFoundHttpException $exception) {
            // If we can not find a route, use the default Themosis template as fallback
            $view = $this->container['view'];
            $response = $view->make('_themosisNoRoute');
            return $this->prepareResponse($request, $response);
        }
    }
} 