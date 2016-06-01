<?php

namespace Themosis\Asset;

use Themosis\Foundation\Application;

class AssetFactory
{
    /**
     * The AssetFinder instance.
     *
     * @var AssetFinder
     */
    protected $finder;

    /**
     * The service container.
     *
     * @var \Themosis\Foundation\Application
     */
    protected $container;

    /**
     * Alias prefix in order to register
     * assets into the service container.
     *
     * @var string
     */
    protected $aliasPrefix = 'asset';

    /**
     * A list of authorized assets to add.
     *
     * @var array
     */
    protected $allowedAssets = ['script', 'style', 'js', 'css'];

    /**
     * Constructor.
     *
     * @param AssetFinder                      $finder
     * @param \Themosis\Foundation\Application $container
     */
    public function __construct(AssetFinder $finder, Application $container)
    {
        $this->finder = $finder;
        $this->container = $container;
    }

    /**
     * Add an asset to the application.
     *
     * NOTE : By default the path is relative to one of the registered
     * paths. Make sure your asset is unique by handle and paths/url.
     * You can also pass an external url.
     *
     * @param string      $handle  The asset handle name.
     * @param string      $path    The URI to the asset or the absolute URL.
     * @param array|bool  $deps    An array with asset dependencies or false.
     * @param string      $version The version of your asset.
     * @param bool|string $mixed   Boolean if javascript file | String if stylesheet file.
     * @param string      $type    'script' or 'style'.
     *
     * @return Asset|\WP_Error
     *
     * @throws AssetException
     */
    public function add($handle, $path, $deps = [], $version = '1.0', $mixed = null, $type = '')
    {
        if (!is_string($handle) && !is_string($path)) {
            throw new AssetException('Invalid parameters for [Asset::add] method.');
        }

        // Init type.
        $t = '';

        // Get full URL for the asset.
        $path = $this->finder->find($path);

        // Group arguments.
        $args = compact('handle', 'path', 'deps', 'version', 'mixed');

        // Get file extension.
        $ext = pathinfo($path, PATHINFO_EXTENSION);

        // Define the asset type.
        if (!empty($type) && in_array($type, $this->allowedAssets)) {
            $t = $type;
        } elseif ($ext) {
            $t = ($ext === 'css') ? 'style' : 'script';
        }

        /*
         * Check the asset type is defined.
         */
        if (empty($t)) {
            return new \WP_Error('asset', sprintf('%s: %s. %s', __("Can't load your asset", THEMOSIS_FRAMEWORK_TEXTDOMAIN), $handle, __('If your asset has no file extension, please provide the type parameter.', THEMOSIS_FRAMEWORK_TEXTDOMAIN)));
        }

        // Register the asset into the service container
        // and return it for chaining.
        // Assets are shared, so only one instance of each is available
        // into the container.
        // Assets are registered using the 'asset' prefix followed
        // by their unique asset handle: 'asset.unique-handle'
        $asset = new Asset($t, $args, $this->container['action'], $this->container['html'], $this->container['filter']);
        $this->container->instance($this->aliasPrefix.'.'.$handle, $asset);
        return $asset;
    }
}
