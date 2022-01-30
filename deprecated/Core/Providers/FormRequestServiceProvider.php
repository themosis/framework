<?php

namespace Themosis\Core\Providers;

use Illuminate\Contracts\Validation\ValidatesWhenResolved;
use Illuminate\Routing\Redirector;
use Illuminate\Support\ServiceProvider;
use Themosis\Core\Http\FormRequest;

class FormRequestServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap the service.
     */
    public function boot()
    {
        $this->app->afterResolving(ValidatesWhenResolved::class, function ($resolved) {
            $resolved->validateResolved();
        });

        $this->app->resolving(FormRequest::class, function ($request, $app) {
            $request = FormRequest::createFrom($app['request'], $request);

            $request->setContainer($app)->setRedirector($app->make(Redirector::class));
        });
    }
}
