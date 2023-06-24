<?php

namespace Themosis\PostType;

use Illuminate\Support\ServiceProvider;

class PostTypeServiceProvider extends ServiceProvider
{
    public function register()
    {
        /** @var \Illuminate\View\Factory $view */
        $view = $this->app['view'];
        $view->addLocation(__DIR__.'/views');

        $this->app->bind('posttype', function ($app) {
            return new Factory($app, $app['action'], $app['filter']);
        });
    }
}
