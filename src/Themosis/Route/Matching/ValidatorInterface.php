<?php

namespace Themosis\Route\Matching;

use Illuminate\Http\Request;
use Themosis\Route\Route;

interface ValidatorInterface {

    /**
     * Validate a given rule against a route and request.
     *
     * @param  \Themosis\Route\Route $route
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function matches(Route $route, Request $request);

}
