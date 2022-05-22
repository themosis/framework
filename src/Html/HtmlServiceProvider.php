<?php

namespace Themosis\Html;

use Illuminate\Support\ServiceProvider;

class HtmlServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(HtmlBuilder::class, function () {
            return new HtmlBuilder();
        });
    }
}
