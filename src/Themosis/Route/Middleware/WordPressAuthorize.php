<?php

namespace Themosis\Route\Middleware;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class WordPressAuthorize
{
    /**
     * Handle incoming request.
     *
     * @param Request $request
     * @param \Closure $next
     * @param string $capability
     *
     * @throws AuthenticationException
     *
     * @return mixed
     */
    public function handle($request, \Closure $next, $capability = 'edit_posts')
    {
        /**
         * Verify that current user is logged in and has appropriate capability.
         */
        if (is_user_logged_in() && current_user_can($capability)) {
            return $next($request);
        }

        throw new AuthenticationException('Unauthorized request.');
    }
}
