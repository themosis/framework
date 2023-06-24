<?php

namespace Themosis\Core\Http\Middleware;

use Closure;
use Illuminate\Contracts\Foundation\Application;

class PreventRequestsDuringMaintenance
{
    /**
     * The application.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The URIs that should be accessible while maintenance mode is enabled.
     *
     * @var array
     */
    protected $except = [];

    /**
     * Create a new middleware instance.
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function handle($request, Closure $next)
    {
        if ($this->app->isDownForMaintenance()) {
            // @todo Adapt with WordPress maintenance mode.
        }

        return $next($request);
    }
}
