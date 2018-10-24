<?php

namespace Themosis\Core\Providers;

use Illuminate\Database\MigrationServiceProvider;
use Illuminate\Support\AggregateServiceProvider;

class ConsoleCoreServiceProvider extends AggregateServiceProvider
{
    /**
     * Defer the loading of the provider.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * The provider class names.
     *
     * @var array
     */
    protected $providers = [
        ConsoleServiceProvider::class,
        MigrationServiceProvider::class,
        ComposerServiceProvider::class
    ];
}
