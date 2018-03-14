<?php

namespace Themosis\Hook;

use Illuminate\Support\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('action', function ($container) {
            return new ActionBuilder($container);
        });

        $this->app->bind('filter', function ($container) {
            return new FilterBuilder($container);
        });
    }
}
