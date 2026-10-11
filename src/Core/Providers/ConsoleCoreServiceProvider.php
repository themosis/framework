<?php

namespace Themosis\Core\Providers;

use Illuminate\Support\AggregateServiceProvider;

class ConsoleCoreServiceProvider extends AggregateServiceProvider
{
    /**
     * The provider class ConsoleCoreServiceProvider.
     *
     * @var array
     */
    protected $providers = [
        ArtisanServiceProvider::class,
        ComposerServiceProvider::class,
    ];
}
