<?php

namespace Themosis\Action;

use Themosis\Foundation\ServiceProvider;

class ActionServiceProvider extends ServiceProvider
{
    protected $provides = [
        'action',
    ];

    public function register()
    {
        $container = $this->getContainer();
        $container->add('action', 'Themosis\Action\ActionBuilder')->withArgument($container);
    }
}
