<?php

namespace Themosis\Route\Matching;

use Themosis\Foundation\Request;
use Themosis\Route\Route;
use Themosis\Route\WordPressRoute;

interface IWordPressMatching
{
    /**
     * Validate a given rule against a route and request.
     *
     * @param \Themosis\Route\WordPressRoute        $route
     * @param \Themosis\Foundation\Request $request
     *
     * @return bool
     */
    public function matches(WordPressRoute $route, Request $request);
}
