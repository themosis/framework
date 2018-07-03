<?php

namespace Themosis\Page;

use Illuminate\Support\ServiceProvider;

class PageServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('page', function ($app) {
            // Add framework view path for page views.
            $viewFactory = $app['view'];
            $viewFactory->addLocation(__DIR__.'/views');

            return new PageFactory($app['action'], $viewFactory);
        });
    }
}
