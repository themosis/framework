<?php
namespace Themosis\Asset;

class AssetFactory
{
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
     * @param array|boolean $deps An array with asset dependencies or false.
     * @param string $version The version of your asset.
     * @param bool|string $mixed Boolean if javascript file | String if stylesheet file.
     * @param string $type 'script' or 'style'.
     * @return Asset|\WP_Error
     * @throws AssetException
     */
    public function add($handle, $path, $deps = [], $version = '1.0', $mixed = null, $type = '')
    {
        if (!is_string($handle) && !is_string($path)) throw new AssetException("Invalid parameters for [Asset::add] method.");

        $t = '';
        $path = $this->finder->find($path);
        $args = compact('handle', 'path', 'deps', 'version', 'mixed');

        // Check if asset has an extension.
        $ext = pathinfo($path, PATHINFO_EXTENSION);

        // If extension.
        if ($ext)
        {
            // Check the type of asset.
            $t = ($ext === 'css') ? 'style' : 'script';
        }
        elseif (!empty($type) && in_array($type, ['style', 'script']))
        {
            $t = $type;
        }

        // Check the asset type.
        if (empty($t)) return new \WP_Error('asset', __("Can't load your asset: {$handle}. If your asset has no file extension, please provide the type parameter.", THEMOSIS_FRAMEWORK_TEXTDOMAIN));

        // Return the asset instance.
        return new Asset($t, $args);
    }

}
