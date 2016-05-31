<?php

namespace Themosis\Html;

use Themosis\Foundation\ServiceProvider;

class FormServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('form', function ($container) {
            return new FormBuilder($container['html'], $container['request']);
        });
    }
}
