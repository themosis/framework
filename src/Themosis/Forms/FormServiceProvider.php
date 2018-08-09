<?php

namespace Themosis\Forms;

use Illuminate\Support\ServiceProvider;
use League\Fractal\Manager;

class FormServiceProvider extends ServiceProvider
{
    /**
     * Register our form service.
     */
    public function register()
    {
        $this->registerFractalManager();

        $this->app->singleton('form', function ($app) {
            $view = $app['view'];
            $view->addLocation(__DIR__.'/views');

            return new FormFactory($app['validator'], $view, $app['league.fractal']);
        });
    }

    /**
     * Register the PHP League Fractal manager class.
     */
    protected function registerFractalManager()
    {
        $this->app->bind('league.fractal', function () {
            return new Manager();
        });
    }
}
