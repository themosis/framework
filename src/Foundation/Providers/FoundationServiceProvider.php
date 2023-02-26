<?php

namespace Themosis\Foundation\Providers;

use Illuminate\Support\AggregateServiceProvider;
use Themosis\Asset\AssetServiceProvider;

class FoundationServiceProvider extends AggregateServiceProvider
{
    protected $providers = [
        AssetServiceProvider::class,
        ThemeServiceProvider::class,
    ];
}
