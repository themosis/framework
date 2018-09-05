<?php

namespace Themosis\Metabox;

use Illuminate\Support\ServiceProvider;
use League\Fractal\Manager;
use League\Fractal\Serializer\ArraySerializer;
use Themosis\Forms\Fields\FieldsRepository;
use Themosis\Metabox\Resources\MetaboxResource;
use Themosis\Metabox\Resources\Transformers\MetaboxTransformer;
use Themosis\Support\Facades\Route;

class MetaboxServiceProvider extends ServiceProvider
{
    /**
     * Defer metabox factory.
     *
     * @var bool
     */
    protected $defer = true;

    public function register()
    {
        $this->app->bind('metabox', function ($app) {
            $resource = new MetaboxResource(
                $app->bound('league.fractal') ? $app['league.fractal'] : new Manager(),
                new ArraySerializer(),
                new MetaboxTransformer()
            );

            return new Factory($app, $app['action'], $resource, new FieldsRepository());
        });
    }

    /**
     * Return list of registered bindings.
     *
     * @return array
     */
    public function provides()
    {
        return ['metabox'];
    }

    public function boot()
    {
        /**
         * Register the metabox API routes.
         */
        Route::middleware('wpadmin')
            ->namespace('Themosis\Metabox\Controllers')
            ->prefix('wp-api/themosis/v1')
            ->group(__DIR__.'/routes.php');
    }
}
