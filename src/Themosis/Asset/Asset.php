<?php

namespace Themosis\Asset;

use Themosis\Hook\IHook;
use Themosis\Html\HtmlBuilder;

class Asset implements IAsset
{
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
    protected $allowedAreas = ['admin', 'login', 'customizer'];

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

    /**
     * A list of enqueued assets.
     *
     * @var array
     */
    protected static $instantiated;

    /**
     * @var \Themosis\Hook\ActionBuilder
     */
    protected $action;

    /**
     * @var \Themosis\Html\HtmlBuilder
     */
    protected $html;

    /**
     * @var \Themosis\Hook\FilterBuilder
     */
    protected $filter;

    /**
     * Build an Asset instance.
     *
     * @param string                     $type
     * @param array                      $args
     * @param \Themosis\Hook\IHook       $action
     * @param \Themosis\Html\HtmlBuilder $html
     * @param \Themosis\Hook\IHook       $filter
     */
    public function __construct($type, array $args, IHook $action, HtmlBuilder $html, IHook $filter)
    {
        $this->type = $type;
        $this->args = $this->parse($args);
        $this->key = strtolower(trim($args['handle']));
        $this->action = $action;
        $this->html = $html;
        $this->filter = $filter;

        $this->registerInstance();

        // Listen to WordPress asset events.
        $action->add('wp_enqueue_scripts', [$this, 'install']);
        $action->add('admin_enqueue_scripts', [$this, 'install']);
        $action->add('login_enqueue_scripts', [$this, 'install']);
        $action->add('customize_preview_init', [$this, 'install']);
    }

    /**
     * Parse defined asset properties.
     * 
     * @param array $args The asset properties.
     *
     * @return mixed
     */
    protected function parse(array $args)
    {
        /*
         * Parse version.
         */
        $args['version'] = $this->parseVersion($args['version']);

        /*
         * Parse mixed.
         */
        $args['mixed'] = $this->parseMixed($args['mixed']);

        return $args;
    }

    /**
     * Parse the version number.
     *
     * @param string|bool|null $version
     *
     * @return mixed
     */
    protected function parseVersion($version)
    {
        if (is_string($version)) {
            if (empty($version)) {
                // Passing empty string is equivalent to set it to null.
                return;
            }
            // Return the defined string version.
            return $version;
        } elseif (is_null($version)) {
            // Return null.
            return;
        }

        // Version can only be a string or null. If anything else, return false.
        return false;
    }

    /**
     * Parse the mixed argument.
     *
     * @param $mixed
     *
     * @return string|bool
     */
    protected function parseMixed($mixed)
    {
        if ('style' === $this->type) {
            $mixed = (is_string($mixed) && !empty($mixed)) ? $mixed : 'all';
        } elseif ('script' === $this->type) {
            $mixed = is_bool($mixed) ? $mixed : false;
        }

        return $mixed;
    }

    /**
     * Register asset instances.
     */
    protected function registerInstance()
    {
        if (isset(static::$instances[$this->area][$this->key])) {
            return;
        }

        static::$instances[$this->area][$this->key] = $this;
    }

    /**
     * Allow the developer to define where to load the asset.
     * Only 'admin', 'login' and 'customizer' are accepted. If none of those
     * values are used, simply keep the default front-end area.
     *
     * @param string $area Specify where to load the asset: 'admin', 'login' or 'customizer'.
     *
     * @return Asset
     */
    public function to($area)
    {
        if (is_string($area) && in_array($area, $this->allowedAreas)) {
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
     * @param mixed  $data       Any data to attach to the JS variable: string, boolean, object, array, ...
     *
     * @return Asset
     */
    public function localize($objectName, $data)
    {
        if ('script' === $this->type) {
            $this->args['localize'][$objectName] = $data;
        }

        return $this;
    }

    /**
     * Remove a declared asset.
     *
     * @return Asset
     */
    public function remove()
    {
        if ($this->isQueued()) {
            unset(static::$instances[$this->area][$this->key]);
        }

        return $this;
    }

    /**
     * Tells if an asset is queued or not.
     *
     * @return bool
     */
    public function isQueued()
    {
        if (isset(static::$instances[$this->area][$this->key])) {
            return true;
        }

        return false;
    }

    /**
     * Add inline code before or after the loaded asset.
     * Default to "after".
     *
     * @param string $data     The inline code to output.
     * @param string $position Accepts "after" or "before" as values. Note that position is only working for JS assets.
     *
     * @return Asset
     */
    public function inline($data, $position = 'after')
    {
        if ('script' === $this->type) {
            $args = [
                'data' => $data,
                'position' => $position,
            ];
        } elseif ('style' === $this->type) {
            $args = [
                'data' => $data,
            ];
        }

        $this->args['inline'][] = $args;

        return $this;
    }

    /**
     * Add attributes to the asset opening tag.
     *
     * @param array $atts The asset attributes to add.
     *
     * @return Asset
     */
    public function addAttributes(array $atts)
    {
        $html = $this->html;
        $key = $this->key;

        $replace = function ($tag, $atts, $append) use ($html) {
            if (false !== $pos = strrpos($tag, $append)) {
                $tag = substr_replace($tag, $html->attributes($atts), $pos).' '.trim($append);
            }

            return $tag;
        };

        if ('script' === $this->type) {
            $append = '></script>';
            $this->filter->add('script_loader_tag', function ($tag, $handle) use ($atts, $append, $replace, $key) {
                // Check we're only filtering the current asset and not all.
                if ($key === $handle) {
                    return $replace($tag, $atts, $append);
                }

                return $tag;
            });
        }

        if ('style' === $this->type) {
            $append = ' />';
            $this->filter->add('style_loader_tag', function ($tag, $handle) use ($atts, $append, $replace, $key) {
                // Check we're only filtering the current asset and not all.
                if ($key === $handle) {
                    return $replace($tag, $atts, $append);
                }

                return $tag;
            }, 4);
        }

        return $this;
    }

    /**
     * Manipulate the static::$instances variable
     * in order to separate each asset in its area.
     */
    protected function orderInstances()
    {
        if (array_key_exists($this->key, static::$instances['front'])) {
            unset(static::$instances['front'][$this->key]);
            static::$instances[$this->area][$this->key] = $this;
        }
    }

    /**
     * Install the appropriate asset depending of its area.
     */
    public function install()
    {
        $from = current_filter();

        switch ($from) {
            // Front-end assets.
            case 'wp_enqueue_scripts':

                if (isset(static::$instances['front']) && !empty(static::$instances['front'])) {
                    foreach (static::$instances['front'] as $asset) {
                        // Check if asset has not yet been called...
                        if (isset(static::$instantiated['front'][$asset->getKey()])) {
                            return;
                        }

                        $this->register($asset);
                    }
                }

                break;

            // WordPress admin assets.
            case 'admin_enqueue_scripts':

                if (isset(static::$instances['admin']) && !empty(static::$instances['admin'])) {
                    foreach (static::$instances['admin'] as $asset) {
                        // Check if asset has not yet been called...
                        if (isset(static::$instantiated['admin'][$asset->getKey()])) {
                            return;
                        }

                        $this->register($asset);
                    }
                }

                break;

            // Login assets.
            case 'login_enqueue_scripts':

                if (isset(static::$instances['login']) && !empty(static::$instances['login'])) {
                    foreach (static::$instances['login'] as $asset) {
                        // Check if asset has not yet been called...
                        if (isset(static::$instantiated['login'][$asset->getKey()])) {
                            return;
                        }

                        $this->register($asset);
                    }
                }

                break;

            case 'customize_preview_init':

                if (isset(static::$instances['customizer']) && !empty(static::$instances['customizer'])) {
                    foreach (static::$instances['customizer'] as $asset) {
                        // Check if asset has not yet been called...
                        if (isset(static::$instantiated['customizer'][$asset->getKey()])) {
                            return;
                        }

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
     */
    protected function register(Asset $asset)
    {
        // Avoid duplicate calls to each instance.
        if ($this->getArea() !== $asset->getArea()) {
            return;
        }

        // Register asset.
        if ($asset->getType() === 'script') {
            $this->registerScript($asset);
        } else {
            $this->registerStyle($asset);
        }

        // Add asset to list of called instances.
        static::$instantiated[$this->getArea()][$this->getKey()] = $this;
    }

    /**
     * Register a 'script' asset.
     *
     * @param Asset $asset
     */
    protected function registerScript(Asset $asset)
    {
        $args = $asset->getArgs();
        wp_enqueue_script($args['handle'], $args['path'], $args['deps'], $args['version'], $args['mixed']);

        // Add localized data for scripts.
        if (isset($args['localize']) && !empty($args['localize'])) {
            foreach ($args['localize'] as $objectName => $data) {
                wp_localize_script($args['handle'], $objectName, $data);
            }
        }

        // Pass the asset instance and register inline code.
        $this->registerInline($asset);
    }

    /**
     * Register a 'style' asset.
     *
     * @param Asset $asset
     */
    protected function registerStyle(Asset $asset)
    {
        $args = $asset->getArgs();
        wp_enqueue_style($args['handle'], $args['path'], $args['deps'], $args['version'], $args['mixed']);

        // Pass the asset instance and register inline code.
        $this->registerInline($asset);
    }

    /**
     * Register inline code.
     *
     * @param Asset $asset
     */
    protected function registerInline(Asset $asset)
    {
        $args = $asset->getArgs();

        if (empty($args) || !isset($args['inline'])) {
            return;
        }

        // Process if there are inline codes.
        $inlines = $args['inline'];

        foreach ($inlines as $inline) {
            if ('script' === $asset->getType()) {
                wp_add_inline_script($args['handle'], $inline['data'], $inline['position']);
            } elseif ('style' === $asset->getType()) {
                wp_add_inline_style($args['handle'], $inline['data']);
            }
        }
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
     * Return the asset properties. If $name isset, return its value.
     * If nothing is defined, return all properties.
     *
     * @param string $name The argument name.
     *
     * @return array|string
     */
    public function getArgs($name = '')
    {
        if (!empty($name) && array_key_exists($name, $this->args)) {
            return $this->args[$name];
        }

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
