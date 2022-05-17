<?php

namespace Themosis\Foundation\Theme;

use Composer\Autoload\ClassLoader;
use Illuminate\Support\ServiceProvider;
use Themosis\Hook\Filter;

class ThemeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerThemeManager();
    }

    protected function registerThemeManager(): void
    {
        $this->app->bind('themosis.theme', function ($app) {
            return new Manager($app, new ClassLoader(), $app['config'], $app[Filter::class]);
        });
    }
}
