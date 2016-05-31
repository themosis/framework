<?php

namespace Themosis\Field;

use Themosis\Foundation\ServiceProvider;

class FieldServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('field', function ($container) {
            return new FieldFactory($container['view']);
        });
    }
}
