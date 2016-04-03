<?php
namespace Themosis\Route;

use Closure;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Illuminate\Container\Container as IlluminateContainer;
use Illuminate\Events\Dispatcher as IlluminateDispatcher;
use Illuminate\Http\Request as IlluminateRequest;
use Illuminate\Routing\Router as IlluminateRouter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Themosis\Action\Action;

class Router extends IlluminateRouter
{

    public function __construct(IlluminateContainer $container)
    {
        $events = new IlluminateDispatcher(new IlluminateContainer());

        parent::__construct($events, $container);

        $this->routes = new RouteCollection;

        Action::listen('themosis_routes_loaded', $this, 'addRewriteRules')->dispatch();
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

    /**
     * Registers rewrite rules for every non-WordPress conditional route.
     * This way force WordPress not to change the url to one WordPress knows
     */
    protected function addRewriteRules()
    {
        foreach ($this->routes as $route) {
            if ($route instanceof Route) {

                if (!$route->condition()) {
                    // Compile the route to get a Symfony compiled route
                    $route->compileRoute();
                    // Get the regex of the compiled route
                    $routeRegex = $route->getCompiled()->getRegex();
                    // Remove the first part (#^/) of the regex because WordPress adds this already by itself
                    $routeRegex = preg_replace('/^\#\^\//', '^', $routeRegex);
                    // Remove the last part (#s$) of the regex because WordPress adds this already by itself
                    $routeRegex = preg_replace('/\#[s]$/', '$', $routeRegex);

                    // Add the rewrite rule to the top
                    add_action('init', function () use ($routeRegex) {
                        add_rewrite_rule($routeRegex, 'index.php', 'top');
                    });

                }
            }
        }
    }
} 