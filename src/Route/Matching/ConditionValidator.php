<?php

namespace Themosis\Route\Matching;

use Illuminate\Http\Request;
use Illuminate\Routing\Matching\ValidatorInterface;
use Illuminate\Routing\Route;

class ConditionValidator implements ValidatorInterface
{
    /**
     * Validate a given rule against a route and request.
     *
     * @param Route   $route
     * @param Request $request
     *
     * @return bool
     */
    public function matches(Route $route, Request $request): bool
    {
        /** @var \Themosis\Route\Route $route */
        if (call_user_func_array($route->getCondition(), $route->getConditionParameters())) {
            return true;
        }

        return false;
    }
}
