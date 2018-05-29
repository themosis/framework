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
            // Add framework view path for form and types views.
            $viewFactory = $app['view'];
            $viewFactory->addLocation(__DIR__.'/views');

            return new FormFactory($app['validator'], $viewFactory);
        });
    }
}
