<?php

namespace Themosis\Config;

use Themosis\Foundation\ServiceProvider;

class ConfigServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->instance('config.finder', new ConfigFinder());

        $this->app->singleton('config', function($container)
        {
            return new ConfigFactory($container['config.finder']);
        });
    }
}
