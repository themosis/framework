<?php

namespace Themosis\Route;

use Illuminate\Routing\RouteCollection as IlluminateRouteCollection;

class RouteCollection extends IlluminateRouteCollection
{
    /**
     * Add the given route to the arrays of routes.
     *
     * @param \Themosis\Route\Route $route
     */
    protected function addToCollections($route)
    {
        if (method_exists($route, 'getUri')) {
            $domainAndUri = $route->domain().$route->getUri();
        } else {
            $domainAndUri = $route->domain().$route->uri();
        }

        if ($route->condition() && $route->conditionalParameters()) {
            $domainAndUri .= serialize($route->conditionalParameters());
        }

        foreach ($route->methods() as $method) {
            $this->routes[$method][$domainAndUri] = $route;
        }

        $this->allRoutes[$method.$domainAndUri] = $route;
    }

    /**
     * Determine if a route in the array matches the request.
     *
     * @param array                    $routes
     * @param \Illuminate\http\Request $request
     * @param bool                     $includingMethod
     *
     * @return \Illuminate\Routing\Route|null
     */
    protected function check(array $routes, $request, $includingMethod = true)
    {
        $foundRoute = parent::check($routes, $request, $includingMethod);

        // If no route found, check if the 404 route is set, if so, return that route as our found route.
        if (!$foundRoute && isset($routes['404'])) {
            $foundRoute = $routes['404'];
        }

        return $foundRoute;
    }
}
