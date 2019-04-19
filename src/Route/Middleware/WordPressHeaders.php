<?php

namespace Themosis\Route\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WordPressHeaders
{
    /**
     * Cleanup response headers.
     *
     * @param Request  $request
     * @param \Closure $next
     *
     * @return Response
     */
    public function handle(Request $request, \Closure $next)
    {
        $route = $request->route();
        $response = $next($request);

        if (! $route->hasCondition() && function_exists('is_user_logged_in') && ! is_user_logged_in()) {
            // We're on a custom route. Remove "no-cache" headers added by WordPress:
            // - Cache-Control
            // - Expires
            // - Content-type (provided by the response instance as well)
            @header_remove('Cache-Control');
            @header_remove('Expires');
            @header_remove('Content-Type');
        }

        // Set the response cache control to "public"
        // on pages visited by guest users only.
        if (function_exists('is_user_logged_in') && ! is_user_logged_in()) {
            $response->setPublic();
        }

        return $response;
    }
}
