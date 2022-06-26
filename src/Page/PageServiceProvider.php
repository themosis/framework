<?php

namespace Themosis\Page;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\Factory;

class PageServiceProvider extends ServiceProvider
{
    /**
     * Defer page factory.
     *
     * @var bool
     */
    protected $defer = true;

    public function register()
    {
        /** @var Factory $view */
        $view = $this->app['view'];
        $view->addLocation(__DIR__.'/views');

        $this->app->bind('page', function ($app) {
            return new PageFactory($app['action'], $app['filter'], $app['view'], $app['validator']);
        });
    }

    /**
     * Return list of registered bindings.
     *
     * @return array
     */
    public function provides()
    {
        return ['page'];
    }
}
