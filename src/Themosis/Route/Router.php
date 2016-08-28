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
     * Create a new Themosis Route object.
     *
     * @param  array|string $methods
     * @param  string       $uri
     * @param  mixed        $action
     * @return \Illuminate\Routing\Route
     */
    protected function newRoute($methods, $uri, $action)
    {
        return (new Route($methods, $uri, $action))
            ->setRouter($this)
            ->setContainer($this->container);
    }

    /**
     * Find the route matching a given request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Themosis\Route\Route
     */
    protected function findRoute($request)
    {
        $route = parent::findRoute($request);

        // If the current route is a WordPress route
        if ($route instanceof Route && !$route->condition()) {

            global $wp, $wp_query;

            //Check if the route is not a WordPress route and the matched rule is not the the regex of the matched route
            if ($wp->matched_rule != $route->getRewriteRuleRegex()) {
                // Flush the rewrite rules
                flush_rewrite_rules();
            }

            // Reset the WordPress query.
            $wp_query->init();
        }

        return $route;
    }
}
