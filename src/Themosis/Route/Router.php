<?php

namespace Themosis\Route;

use Illuminate\Events\Dispatcher;
use Illuminate\Routing\Router as IlluminateRouter;
use ReflectionClass;
use Themosis\Facades\WPRoute;
use Themosis\Foundation\Application;

class Router extends IlluminateRouter
{
	/**
	 * Whether to create the routes as WordPress routes
	 *
	 * @var boolean
	 */
	protected $createAsWordPressRoute = false;

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
	 * @return boolean
	 */
	public function isCreateAsWordPressRoute()
	{
		return $this->createAsWordPressRoute;
	}

	/**
	 * @param boolean $createAsWordPressRoute
	 *
	 * @return $this
	 */
	public function setCreateAsWordPressRoute($createAsWordPressRoute)
	{
		$this->createAsWordPressRoute = $createAsWordPressRoute;
		return $this;
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
    	$routeClass = 'Themosis\\Route\\' . ($this->createAsWordPressRoute ? 'WordPressRoute' : 'Route');
    	$routeInstance = new ReflectionClass($routeClass);

        return $routeInstance->newInstanceArgs([$methods, $uri, $action])
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
        if ($route instanceof WPRoute && !$route->condition()) {

            global $wp, $wp_query;

            //Check if the route is not a WordPress route and warn the developer to flush the rewrite rules.
            if ($wp->matched_rule != $route->getRewriteRuleRegex()) {
                flush_rewrite_rules();
            }

            // Reset the WordPress query.
            $wp_query->init();
        }

        return $route;
    }
}
