<?php

namespace Themosis\Tinker;

use Laravel\Tinker\TinkerServiceProvider as LaravelTinkerServiceProvider;
use Themosis\Core\Application;

class TinkerServiceProvider extends LaravelTinkerServiceProvider
{
    /**
     * Boot the service provider.
     */
    public function boot()
    {
        $source = realpath($raw = __DIR__.'/config/tinker.php') ?: $raw;

        if ($this->app instanceof Application && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('tinker.php')]);
        }

        $this->mergeConfigFrom($source, 'tinker');
    }
}
