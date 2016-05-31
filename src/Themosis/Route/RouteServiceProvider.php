<?php

namespace Themosis\Route;

use Themosis\Foundation\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('router', function ($container) {
            return new Router($container);
        });
    }
}
