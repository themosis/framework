<?php

namespace Themosis\PostType;

use Illuminate\Support\ServiceProvider;

class PostTypeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('posttype', function ($app) {
            return new Factory($app, $app['action'], $app['filter']);
        });
    }
}
