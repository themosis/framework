<?php

namespace Themosis\Asset;

use Illuminate\Support\ServiceProvider;
use Themosis\Hook\Action;
use Themosis\Hook\Filter;
use Themosis\Html\HtmlBuilder;

class AssetServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Finder::class, function ($app) {
            return (new Finder($app['files']))
                ->addLocations($app['config']['assets.paths']);
        });

        $this->app->singleton(Factory::class, function ($app) {
            return new Factory(
                $app[Finder::class],
                $app[Action::class],
                $app[Filter::class],
                $app[HtmlBuilder::class],
            );
        });
    }
}
