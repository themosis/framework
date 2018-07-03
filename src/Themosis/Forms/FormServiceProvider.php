<?php

namespace Themosis\Forms;

use Illuminate\Support\ServiceProvider;

class FormServiceProvider extends ServiceProvider
{
    /**
     * Register our form service.
     */
    public function register()
    {
        $this->app->singleton('form', function ($app) {
            $view = $app['view'];
            $view->addLocation(__DIR__.'/views');

            return new FormFactory($app['validator'], $view);
        });
    }
}
