<?php

namespace Themosis\Forms;

use Illuminate\Support\ServiceProvider;
use League\Fractal\Manager;
use Themosis\Forms\Resources\Factory;

class FormServiceProvider extends ServiceProvider
{
    /**
     * Register our form service.
     */
    public function register()
    {
        $this->registerFractalManager();

        /** @var \Illuminate\View\Factory $view */
        $view = $this->app['view'];
        $view->addLocation(__DIR__.'/views');

        $this->app->singleton('form', function ($app) {
            return new FormFactory(
                $app['validator'],
                $app['view'],
                $app['league.fractal'],
                new Factory()
            );
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
