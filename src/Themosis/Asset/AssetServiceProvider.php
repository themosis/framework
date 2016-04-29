<?php

namespace Themosis\Asset;

use Themosis\Foundation\ServiceProvider;

class AssetServiceProvider extends ServiceProvider
{
    protected $provides = [
        'asset.finder',
        'asset'
    ];

    public function register()
    {
        $this->getContainer()->add('asset.finder', new AssetFinder());
        $this->getContainer()->add('asset', 'Themosis\Asset\AssetFactory')->withArgument('asset.finder')->withArgument($this->getContainer());
    }
}
