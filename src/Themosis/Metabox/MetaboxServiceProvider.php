<?php

namespace Themosis\Metabox;

use Illuminate\Support\ServiceProvider;

class MetaboxServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('metabox', function ($app) {
            return new Factory($app, $app['action']);
        });
    }
}
