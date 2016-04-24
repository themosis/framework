<?php

namespace Themosis\Field;

use Themosis\Foundation\ServiceProvider;

class FieldServiceProvider extends ServiceProvider
{
    protected $provides = [
        'field'
    ];

    public function register()
    {
        
    }
    /**
     * Ignite a service.
     */
    public function ignite()
    {
        $this->app->bindShared('field', function ($app) {
            return new FieldFactory($app['view']);
        });
    }
}
