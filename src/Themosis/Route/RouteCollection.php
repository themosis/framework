<?php
namespace Themosis\Route;

use Countable;
use Themosis\Core\Request;
use Themosis\Metabox\Meta;

class RouteCollection implements Countable {

    /**
     * All routes of the collection, classified by HTTP
     * methods.
     * 'GET' => array('$condition' => $routeInstance)
     *
     * @var array
     */
    protected static $routes = [];

    /**
     * All routes flattened in the collection.
     * 'GET'.$condition' => $routeInstance
     * @var array
     */
    protected static $allRoutes = [];

    /**
     * A look-up table of routes by their names.
     *
     * @var array
     */
    protected static $nameList = [];

    /**
     * A look-up table of routes by controller action.
     *
     * @var array
     */
    protected static $actionList = [];

    /**
     * Add a Route instance to the collection.
     *
     * @param  \Themosis\Route\Route  $route
     * @return \Themosis\Route\Route
     */
    public function add(Route $route)
    {
        $this->addToCollection($route);
        $this->addLookups($route);

        return $route;
    }

    /**
     * Add the given route to the arrays of routes.
     *
     * @param \Themosis\Route\Route $route
     * @return void
     */
    protected function addToCollection(Route $route)
    {
        foreach ($route->methods() as $method)
        {
            static::$routes[$method][$route->condition()][] = $route;
            static::$allRoutes[$method.$route->condition()][] = $route;
        }
    }

    /**
     * Add the route to any look-up tables if necessary.
     *
     * @param \Themosis\Route\Route $route
     * @return void
     */
    protected function addLookups($route)
    {
        // If the route has a name, we will add it to the name look-up table so that we
        // will quickly be able to find any route associate with a name and not have
        // to iterate through every route every time we need to perform a look-up.
        $action = $route->getAction();

        if(isset($action['as']))
        {
            static::$nameList[$action['as']] = $route;
        }

        // When the route is routing to a controller we will also store the action that
        // is used by the route. This will let us reverse route to controllers while
        // processing a request and easily generate URLs to the given controllers.
        if(isset($action['controller']))
        {
            $this->addToActionList($action, $route);
        }
    }

    /**
     * Add a route to the controller action dictionary.
     *
     * @param array $action
     * @param \Themosis\Route\Route $route
     * @return void
     */
    protected function addToActionList(array $action, $route)
    {
        if(!isset(static::$actionList[$action['controller']]))
        {
            static::$actionList[$action['controller']] = $route;
        }
    }

    /**
     * Find the first route matching a given request.
     *
     * @param \Themosis\Core\Request $request
     * @return \Themosis\Route\Route
     */
    public function match(Request $request)
    {
        // Return a group of registered routes regarding the HTTP method.
        $routes = $this->get($request->getMethod());

        // Check if we find a matching "route" the WordPress way.
        return $this->check($routes, $request);
    }

    /**
     * Find a matching route.
     *
     * @param array $routesByCondition
     * @param Request $request
     * @return null|\Themosis\Route\Route
     */
    protected function check(array $routesByCondition, Request $request)
    {
        foreach($routesByCondition as $routes)
        {
            foreach($routes as $route)
            {
                if(call_user_func($route->condition(), $route->getParams()))
                {
                    // Check if a template is associated and compare it to current route condition.
                    if($this->hasTemplate() && 'themosis_is_template' !== $route->condition()) continue;

                    // Check if http only.
                    if($route->httpOnly())
                    {
                        if($request->isSecure())
                        {
                            continue;
                        }
                    }

                    // Check if https only.
                    if($route->httpsOnly())
                    {
                        if(!$request->isSecure())
                        {
                            continue;
                        }
                    }

                    return $route;
                }
            }
        }

        return null;
    }

    /**
     * Check if the current request is using a page template.
     *
     * @return bool
     */
    protected function hasTemplate()
    {
        $qo = get_queried_object();

        if(is_a($qo, 'WP_Post') && 'page' === $qo->post_type)
        {
            $template = Meta::get($qo->ID, '_themosisPageTemplate');

            if('none' !== $template && !empty($template))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all of the routes in the collection.
     *
     * @param string|null $method
     * @return array
     */
    protected function get($method = null)
    {
        if(is_null($method)) return $this->getRoutes();

        return array_get(static::$routes, $method, []);
    }

    /**
     * Return all routes of the collection.
     *
     * @return array
     */
    public function getRoutes()
    {
        return array_values(static::$allRoutes);
    }

    /**
     * Return the number of routes in the collection.
     * Method from "Countable" interface. -> triggered when using count($obj).
     *
     * @return int
     */
    public function count()
    {
        return count($this->getRoutes());
    }
}