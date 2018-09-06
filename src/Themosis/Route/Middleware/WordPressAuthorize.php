<?php

namespace Themosis\Route\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class WordPressAuthorize
{
    /**
     * Handle incoming request.
     *
     * @param Request  $request
     * @param \Closure $next
     * @param string   $capability
     *
     * @throws HttpException
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

        throw new HttpException(404);
    }
}
