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
        $container->share('action', 'Themosis\Hook\ActionBuilder')->withArgument($container);

        // Register the filter builder.
        $container->share('filter', 'Themosis\Hook\FilterBuilder')->withArgument($container);
    }
}
