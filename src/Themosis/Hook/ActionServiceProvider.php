<?php

namespace Themosis\Hook;

use Themosis\Foundation\ServiceProvider;

class ActionServiceProvider extends ServiceProvider
{
    protected $provides = [
        'action'
    ];

    public function register()
    {
        $container = $this->getContainer();
        $container->add('action', 'Themosis\Hook\ActionBuilder')->withArgument($container);
    }
}
