<?php

namespace Themosis\Core\Providers;

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
        // @TODO Next, provide console providers.
    ];
}
