<?php

namespace Themosis\Foundation\Providers;

use Illuminate\Support\AggregateServiceProvider;
use Themosis\Foundation\Theme\ThemeServiceProvider;

class CoreServiceProvider extends AggregateServiceProvider
{
    protected $providers = [
        ThemeServiceProvider::class,
    ];
}