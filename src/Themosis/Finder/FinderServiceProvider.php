<?php

namespace Themosis\Finder;

use Illuminate\Filesystem\Filesystem;
use Themosis\Foundation\ServiceProvider;

class FinderServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('filesystem', function () {
            return new Filesystem();
        });
    }
}
