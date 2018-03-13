<?php

namespace Themosis\Validation;

use Themosis\Foundation\ServiceProvider;

class ValidationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('validation', function () {
            return new ValidationBuilder();
        });
    }
}
