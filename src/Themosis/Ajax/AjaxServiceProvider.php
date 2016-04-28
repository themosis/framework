<?php

namespace Themosis\Ajax;

use Themosis\Foundation\ServiceProvider;

class AjaxServiceProvider extends ServiceProvider
{
    protected $provides = [
        'ajax'
    ];

    public function register()
    {
        $this->getContainer()->add('ajax', 'Themosis\Ajax\AjaxBuilder')->withArgument('action');
    }
}
