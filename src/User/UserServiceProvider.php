<?php

namespace Themosis\User;

use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('themosis.user', function ($app) {
            return new Factory($app['validator']);
        });
    }
}
