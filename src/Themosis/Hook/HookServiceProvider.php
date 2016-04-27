<?php

namespace Themosis\Hook;

use Themosis\Foundation\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    protected $provides = [
        'action',
        'filter'
    ];

    public function register()
    {
        $container = $this->getContainer();

        // Register the action builder.
        $container->add('action', 'Themosis\Hook\ActionBuilder')->withArgument($container);

        // Register the filter builder.
        $container->add('filter', 'Themosis\Hook\FilterBuilder')->withArgument($container);
    }
}
