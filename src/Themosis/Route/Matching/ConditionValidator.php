<?php

namespace Themosis\Route\Matching;

use Illuminate\Http\Request;
use Themosis\Route\Route;

class ConditionValidator implements ValidatorInterface {

    /**
     * Validate a given rule against a route and request.
     *
     * @param  \Themosis\Route\Route $route
     * @param  \Illuminate\Http\Request $request
     * @return bool
     */
    public function matches(Route $route, Request $request)
    {
        // Check if a template is associated and compare it to current route condition.
        if ($route->condition() && call_user_func($route->condition(), $route->conditionalParameters())) {
            return true;
        }

        return false;
    }
}
