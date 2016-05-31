<?php

namespace Themosis\Asset;

use Themosis\Foundation\ServiceProvider;

class AssetServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->instance('asset.finder', new AssetFinder());
        $this->app->bind('asset', function ($container) {
            return new AssetFactory($container['asset.finder'], $container);
        });
    }
}
