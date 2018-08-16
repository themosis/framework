<?php

namespace Themosis\Html;

use Illuminate\Support\ServiceProvider;

class HtmlServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('html', function () {
            return new HtmlBuilder();
        });
    }
}
