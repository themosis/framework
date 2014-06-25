<?php
namespace Themosis\Asset;

class AssetFactory {

    /**
     * The AssetFinder instance.
     *
     * @var AssetFinder
     */
    protected $finder;

    /**
     * Build an AssetBuilder instance.
     *
     * @param AssetFinder $finder
     */
    public function __construct(AssetFinder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * Add an asset to the application.
     *
     * NOTE : By default the path is relative to one of the registered
     * paths. Make sure your asset is unique by handle and paths/url.
     * You can also pass an external url.
     *
     * @param string $handle The asset handle name.
     * @param string $path The URI to the asset or the absolute URL.
     * @param array $deps An array with asset dependencies.
     * @param string $version The version of your asset.
     * @param bool|string $mixed Boolean if javascript file | String if stylesheet file.
     * @throws AssetException
     * @return \Themosis\Asset\Asset
     */
    public function add($handle, $path, array $deps = array(), $version = '1.0', $mixed = null)
    {
        if(!is_string($handle) && !is_string($path)) throw new AssetException("Invalid parameters for [Asset::add] method.");

        $path = $this->finder->find($path);
        $args = compact('handle', 'path', 'deps', 'version', 'mixed');

        // Check the type of the added asset.
        $type = (pathinfo($path, PATHINFO_EXTENSION) == 'css') ? 'style' : 'script';

        return new Asset($type, $args);
    }

} 