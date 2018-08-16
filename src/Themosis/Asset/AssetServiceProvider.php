<?php

namespace Themosis\Asset;

use Illuminate\Support\ServiceProvider;

class AssetServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('asset.finder', function ($app) {
            return (new Finder($app['files']))
                ->addLocations($app['config']['assets.paths']);
        });

        $this->app->singleton('asset', function ($app) {
            return new Factory($app['asset.finder'], $app['action'], $app['filter'], $app['html']);
        });
    }
}
