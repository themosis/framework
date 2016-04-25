<?php

namespace Themosis\Validation;

use Themosis\Foundation\ServiceProvider;

class ValidationServiceProvider extends ServiceProvider
{
    protected $provides = [
        'validation',
    ];

    public function register()
    {
        $this->getContainer()->share('validation', 'Themosis\Validation\ValidationBuilder');
    }
}
