<?php

namespace Themosis\Asset;

use League\Container\ServiceProvider\AbstractServiceProvider;

class AssetServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        'asset.finder',
        'asset'
    ];

    public function register()
    {
        $paths = apply_filters('themosis_assets', []);

        $this->getContainer()->add('asset.finder', new AssetFinder($paths));
        $this->getContainer()->add('asset', 'Themosis\Asset\AssetFactory')->withArgument('asset.finder');
    }
}
