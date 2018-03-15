<?php

namespace Themosis\Html;

use Themosis\Foundation\ServiceProvider;

class HtmlServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('html', 'Themosis\Html\HtmlBuilder');
    }
}
