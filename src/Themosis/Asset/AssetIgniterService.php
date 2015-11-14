<?php
namespace Themosis\Asset;

use Themosis\Core\IgniterService;

class AssetIgniterService extends IgniterService {

    /**
     * Ignite the service.
     *
     * @return void
     */
    public function ignite()
    {
        $this->registerAssetFinder();
        $this->registerAssetBuilder();
    }

    /**
     * Register the AssetFinder class.
     *
     * @return void
     */
    protected function registerAssetFinder()
    {
        $this->app->bind('asset.finder', function($app)
        {
            // Paths to asset directories.
            $paths = apply_filters('themosisAssetPaths', []);
            return new AssetFinder($paths);
        });
    }

    /**
     * Register the AssetBuilder class.
     *
     * @return void
     */
    protected function registerAssetBuilder()
    {
        $this->app->bind('asset', function($app)
        {
            return new AssetFactory($app['asset.finder']);
        });
    }

}