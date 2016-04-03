<?php
namespace Themosis\Route;

use Illuminate\Routing\Matching\SchemeValidator;
use Illuminate\Routing\RouteCollection as IlluminateRouteCollection;

class RouteCollection extends IlluminateRouteCollection {
    /**
     * Add the given route to the arrays of routes.
     *
     * @param  Route $route
     * @return void
     */
    protected function addToCollections($route)
    {
        $domainAndUri = $route->domain().$route->getUri();

        if ($route->condition() && $route->conditionalParameters()) {
            $domainAndUri .= serialize($route->conditionalParameters());
        }

        foreach ($route->methods() as $method) {
            $this->routes[$method][$domainAndUri] = $route;
        }

        $this->allRoutes[$method.$domainAndUri] = $route;
    }
}