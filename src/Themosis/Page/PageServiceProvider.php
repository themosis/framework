<?php

namespace Themosis\Page;

use Illuminate\Support\ServiceProvider;

class PageServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('page', function ($app) {
            $view = $app['view'];
            $view->addLocation(__DIR__.'/views');

            return new PageFactory($app['action'], $app['view']);
        });
    }
}
