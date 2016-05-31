<?php

namespace Themosis\Route\Matching;

use Themosis\Foundation\Request;
use Themosis\Route\Route;

interface IMatching
{
    /**
     * Validate a given rule against a route and request.
     *
     * @param \Themosis\Route\Route        $route
     * @param \Themosis\Foundation\Request $request
     *
     * @return bool
     */
    public function matches(Route $route, Request $request);
}
