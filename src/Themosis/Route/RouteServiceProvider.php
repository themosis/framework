<?php

namespace Themosis\Route;

use Themosis\Foundation\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    protected $provides = [
        'router'
    ];

    public function register()
    {
        $this->getContainer()->share('router', 'Themosis\Route\Router')->withArgument($this->getContainer());
    }
}
