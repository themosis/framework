<?php

namespace Themosis\Route;

use Illuminate\Routing\RouteCollection as IlluminateRouteCollection;

class RouteCollection extends IlluminateRouteCollection
{
    /**
     * Add the given route to the arrays of routes.
     *
     * @param \Illuminate\Routing\Route $route
     */
    protected function addToCollections($route)
    {
        $domainAndUri = $route->getDomain().$route->uri();

        if ($route->hasCondition() && ! empty($route->getConditionParameters())) {
            $domainAndUri .= serialize($route->getConditionParameters());
        }

        foreach ($route->methods() as $method) {
            $this->routes[$method][$domainAndUri] = $route;
        }

        $this->allRoutes[$method.$domainAndUri] = $route;
    }
}
