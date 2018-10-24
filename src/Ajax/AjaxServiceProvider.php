<?php

namespace Themosis\Ajax;

use Illuminate\Support\ServiceProvider;

class AjaxServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('ajax', function ($app) {
            return new Ajax($app['action']);
        });
    }
}
