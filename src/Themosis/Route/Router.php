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

            //Check if the route is not a WordPress route and warn the developer to flush the rewrite rules.
            if ($wp->matched_rule != $route->getRewriteRuleRegex()) {

                //$text = __("Custom routes are defined in your project. Please flush the rewrite rules.", THEMOSIS_FRAMEWORK_TEXTDOMAIN);
                //@todo Better way to auto flush rewrite rules or warn the developer to do it.
            }

            // Reset the WordPress query.
            $wp_query->init();
        }

        return $route;
    }
}
