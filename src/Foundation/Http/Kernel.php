<?php

namespace Themosis\Foundation\Http;

use Illuminate\Foundation\Http\Kernel as BaseHttpKernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;

class Kernel extends BaseHttpKernel
{
    protected $bootstrappers = [
        \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
        \Themosis\Foundation\Bootstrap\LoadConfiguration::class,
        \Illuminate\Foundation\Bootstrap\HandleExceptions::class,
        \Illuminate\Foundation\Bootstrap\RegisterFacades::class,
        \Illuminate\Foundation\Bootstrap\RegisterProviders::class,
        \Illuminate\Foundation\Bootstrap\BootProviders::class,
    ];

    public function boot(Request $request): void
    {
        $request->enableHttpMethodParameterOverride();

        $this->app->instance('request', $request);

        Facade::clearResolvedInstance('request');

        $this->bootstrap();
    }
}