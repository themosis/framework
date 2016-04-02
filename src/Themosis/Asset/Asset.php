<?php
namespace Themosis\Asset;

use Themosis\Action\Action;

class Asset {

    /**
     * The default area where to load assets.
     *
     * @var string
     */
    protected $area = 'front';

    /**
     * Allowed areas.
     *
     * @var array
     */
    protected $allowedAreas = ['admin', 'login'];

    /**
     * Type of the asset.
     *
     * @var string
     */
    protected $type;

    /**
     * WordPress properties of an asset.
     *
     * @var array
     */
    protected $args;

    /**
     * Asset key name.
     *
     * @var string
     */
    protected $key;

    /**
     * A list of all Asset instances.
     *
     * @var array
     */
    protected static $instances;

    protected static $instantiated;

    /**
     * Build an Asset instance.
     *
     * @param $type
     * @param array $args
     */
    public function __construct($type, array $args)
    {
        $this->type = $type;
        $this->args = $args;
        $this->key = strtolower(trim($args['handle']));

        $this->registerInstance();

        // Listen to WordPress asset events.
        Action::listen('wp_enqueue_scripts', $this, 'install')->dispatch();
        Action::listen('admin_enqueue_scripts', $this, 'install')->dispatch();
        Action::listen('login_enqueue_scripts', $this, 'install')->dispatch();
    }

    /**
     * Register asset instances.
     *
     * @return void
     */
    protected function registerInstance()
    {
        if (isset(static::$instances[$this->area][$this->key])) return;

        static::$instances[$this->area][$this->key] = $this;
    }

    /**
     * Allow the developer to define where to load the asset.
     * Only 'admin' or 'login' are accepted. If none of those
     * values are used, simply keep the default front-end area.
     *
     * @param string $area Specify where to load the asset: 'admin' or 'login'.
     * @return Asset
     */
    public function to($area)
    {
        if (is_string($area) && in_array($area, $this->allowedAreas))
        {
            $this->area = $area;
            $this->orderInstances();
        }

        return $this;
    }

    /**
     * Localize data for the linked asset.
     * Output JS object right before the script output.
     *
     * @param string $objectName The name of the JS variable that will hold the data.
     * @param mixed $data Any data to attach to the JS variable: string, boolean, object, array, ...
     * @return Asset
     */
    public function localize($objectName, $data)
    {
        if ('script' === $this->type)
        {
            $this->args['localize'][$objectName] = $data;
        }

        return $this;
    }

    /**
     * Manipulate the static::$instances variable
     * in order to separate each asset in its area.
     *
     * @return void
     */
    protected function orderInstances()
    {
        if (array_key_exists($this->key, static::$instances['front']))
        {
            unset(static::$instances['front'][$this->key]);
            static::$instances[$this->area][$this->key] = $this;
        }
    }

    /**
     * Install the appropriate asset depending of its area.
     *
     * @return void
     */
    public function install()
    {
        $from = current_filter();

        switch ($from)
        {
            // Front-end assets.
            case 'wp_enqueue_scripts':

                if (isset(static::$instances['front']) && !empty(static::$instances['front']))
                {
                    foreach (static::$instances['front'] as $asset)
                    {
                        // Check if asset has not yet been called...
                        if (isset(static::$instantiated['front'][$asset->getKey()])) return;

                        $this->register($asset);
                    }
                }

                break;

            // WordPress admin assets.
            case 'admin_enqueue_scripts':

                if (isset(static::$instances['admin']) && !empty(static::$instances['admin']))
                {
                    foreach (static::$instances['admin'] as $asset)
                    {
                        // Check if asset has not yet been called...
                        if (isset(static::$instantiated['admin'][$asset->getKey()])) return;

                        $this->register($asset);
                    }
                }

                break;

            // Login assets.
            case 'login_enqueue_scripts':

                if (isset(static::$instances['login']) && !empty(static::$instances['login']))
                {
                    foreach (static::$instances['login'] as $asset)
                    {
                        // Check if asset has not yet been called...
                        if (isset(static::$instantiated['login'][$asset->getKey()])) return;

                        $this->register($asset);
                    }
                }

                break;
        }

    }

    /**
     * Register the asset.
     *
     * @param Asset $asset
     * @return void
     */
    protected function register(Asset $asset)
    {
        // Avoid duplicate calls to each instance.
        if ($this->getArea() !== $asset->getArea()) return;

        if ($asset->getType() === 'script')
        {
            $this->registerScript($asset);
        }
        else
        {
            $this->registerStyle($asset);
        }

        // Add asset to list of called instances.
        static::$instantiated[$this->getArea()][$this->getKey()] = $this;
    }

    /**
     * Register a 'script' asset.
     *
     * @param Asset $asset
     * @return void
     */
    protected function registerScript(Asset $asset)
    {
        $args = $asset->getArgs();

        $footer = (is_bool($args['mixed'])) ? $args['mixed'] : false;
        $version = (is_string($args['version'])) ? $args['version'] : false;

        wp_enqueue_script($args['handle'], $args['path'], $args['deps'], $version, $footer);

        // Add localized data for scripts.
        if (isset($args['localize']) && !empty($args['localize']))
        {
            foreach ($args['localize'] as $objectName => $data)
            {
                wp_localize_script($args['handle'], $objectName, $data);
            }
        }
    }

    /**
     * Register a 'style' asset.
     *
     * @param Asset $asset
     * @return void
     */
    protected function registerStyle(Asset $asset)
    {
        $args = $asset->getArgs();

        $media = (is_string($args['mixed'])) ? $args['mixed'] : 'all';
        $version = (is_string($args['version'])) ? $args['version'] : false;

        wp_enqueue_style($args['handle'], $args['path'], $args['deps'], $version, $media);
    }

    /**
     * Return the asset type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Return the asset properties.
     *
     * @return array
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * Return the asset area.
     *
     * @return string
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Return the asset key.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

} 