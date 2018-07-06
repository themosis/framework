<?php

namespace Themosis\Field;

use Illuminate\Support\ServiceProvider;

class FieldServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('field', function ($app) {
            return new FieldFactory($app);
        });
    }
}
