<?php

namespace Themosis\Foundation;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

abstract class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Create a new service provider instance.
     *
     * @param  \Themosis\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }
}
