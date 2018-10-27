<?php

namespace Themosis\PostType;

use Illuminate\Support\ServiceProvider;

class PostTypeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('posttype', function ($app) {
            /** @var Factory $view */
            $view = $app['view'];
            $view->addLocation(__DIR__.'/views');

            return new Factory($app, $app['action'], $app['filter']);
        });
    }
}
