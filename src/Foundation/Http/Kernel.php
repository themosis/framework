<?php

namespace Themosis\Foundation\Http;

use Illuminate\Foundation\Http\Kernel as BaseHttpKernel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Pipeline;
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
        $this->app->instance('request', $request);

        Facade::clearResolvedInstance('request');

        $this->bootstrap();
    }

    public function front(): void
    {
        $request = $this->app['request'];

        $response = $this->handle($request);
        $response->send();

        $this->terminate($request, $response);
    }

    /**
     * Send the given request through the middleware / router.
     *
     * @param Request $request
     * @return Response
     */
    protected function sendRequestThroughRouter($request): Response
    {
        return (new Pipeline($this->app))
            ->send($request)
            ->through($this->app->shouldSkipMiddleware() ? [] : $this->middleware)
            ->then($this->dispatchToRouter());
    }
}