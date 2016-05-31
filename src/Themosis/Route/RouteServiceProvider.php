<?php

namespace Themosis\Route;

use Illuminate\Events\Dispatcher;
use Themosis\Foundation\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('router', function ($container) {

            $events = new Dispatcher($container);

            return new Router($events, $container);
        });
    }
}
