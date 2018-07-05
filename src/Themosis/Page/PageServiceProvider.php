<?php

namespace Themosis\Page;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\Factory;

class PageServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('page', function ($app) {
            /** @var Factory $view */
            $view = $app['view'];
            $view->addLocation(__DIR__.'/views');

            return new PageFactory($app['action'], $app['view'], $app['validator']);
        });
    }
}
