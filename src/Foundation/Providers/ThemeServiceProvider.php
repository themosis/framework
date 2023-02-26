<?php

namespace Themosis\Foundation\Providers;

use Composer\Autoload\ClassLoader;
use Illuminate\Support\ServiceProvider;
use Themosis\Foundation\Theme\Manager;
use Themosis\Hook\Filter;

class ThemeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerThemeManager();
    }

    protected function registerThemeManager(): void
    {
        $themeManager = new Manager(
            $this->app,
            new ClassLoader(),
            $this->app['config'],
            $this->app[Filter::class],
        );

        $this->app->instance('themosis.theme', $themeManager);
    }
}
