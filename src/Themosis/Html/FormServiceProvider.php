<?php

namespace Themosis\Html;

use Themosis\Foundation\ServiceProvider;

class FormServiceProvider extends ServiceProvider
{
    protected $provides = [
        'form'
    ];

    public function register()
    {
        $this->getContainer()->share('form', 'Themosis\Html\FormBuilder')->withArguments([
            'html',
            'request'
        ]);
    }
}
