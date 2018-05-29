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
            return new FormFactory($app['validator'], $app['view']);
        });
    }
}
