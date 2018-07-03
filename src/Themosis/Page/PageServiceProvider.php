<?php

namespace Themosis\Page;

use Illuminate\Support\ServiceProvider;

class PageServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('page', function ($app) {
            return new PageFactory($app['action']);
        });
    }
}
