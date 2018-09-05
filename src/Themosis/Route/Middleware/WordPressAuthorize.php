<?php

namespace Themosis\Route\Middleware;

use Illuminate\Auth\AuthenticationException;

class WordPressAuthorize
{
    /**
     * Handle incoming request.
     *
     * @param $request
     * @param \Closure $next
     *
     * @throws AuthenticationException
     *
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        /**
         * Verify that currently logged in user can edit posts.
         */
        if (is_user_logged_in() && current_user_can('edit_posts')) {
            return $next($request);
        }

        throw new AuthenticationException('Unauthorized request.');
    }
}
