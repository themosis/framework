<?php

namespace Themosis\Load;

use Themosis\Foundation\ServiceProvider;

class LoaderServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('loader', function () {
            return new Loader();
        });

        $this->app->bind('loader.widget', function ($container) {
            return new WidgetLoader($container['filter']);
        });
    }
}
