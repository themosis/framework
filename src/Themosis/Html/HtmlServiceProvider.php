<?php

namespace Themosis\Html;

use Themosis\Foundation\ServiceProvider;

class HtmlServiceProvider extends ServiceProvider
{
    protected $provides = [
        'html'
    ];

    public function register()
    {
        $this->getContainer()->share('html', 'Themosis\Html\HtmlBuilder');
    }
}
