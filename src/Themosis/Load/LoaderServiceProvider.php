<?php

namespace Themosis\Load;

use Themosis\Foundation\ServiceProvider;

class LoaderServiceProvider extends ServiceProvider
{
    protected $provides = [
        'loader',
        'loader.widget'
    ];

    public function register()
    {
        $this->getContainer()->share('loader', 'Themosis\Load\Loader');
        $this->getContainer()->share('loader.widget', 'Themosis\Load\WidgetLoader')->withArgument('filter');
    }
}
