<?php

namespace Themosis\Hook;

use Illuminate\Support\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(Action::class, function ($container) {
            return new Action($container);
        });

        $this->app->bind(Filter::class, function ($container) {
            return new Filter($container);
        });
    }
}
