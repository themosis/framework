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
        foreach ($route->methods() as $method) {
            if ($route->condition() && $route->conditionalParameters()) {
                $this->routes[$method][$route->domain() . $route->getUri() . serialize($route->conditionalParameters())] = $route;
            } else {
                $this->routes[$method][$route->domain() . $route->getUri()] = $route;
            }
        }

        if ($route->condition() && $route->conditionalParameters()) {
            $this->allRoutes[$method . $route->domain() . $route->getUri() . serialize($route->conditionalParameters())] = $route;
        } else {
            $this->allRoutes[$method . $route->domain() . $route->getUri()] = $route;
        }
    }
}