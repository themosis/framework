<?php

namespace Themosis\Route;

use Illuminate\Routing\RoutingServiceProvider;

class RouteServiceProvider extends RoutingServiceProvider
{
    public function registerRouter()
    {
        $this->app->singleton('router', function ($app) {
            return new Router($app['events'], $app);
        });
    }
}
