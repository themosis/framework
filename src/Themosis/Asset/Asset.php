<?php

namespace Themosis\Asset;

use Themosis\Hook\IHook;

class Asset implements AssetInterface
{
    /**
     * @var string
     */
    protected $handle;

    /**
     * @var AssetFileInterface
     */
    protected $file;

    /**
     * @var bool|string|array
     */
    protected $dependencies;

    /**
     * @var null|string|bool
     */
    protected $version;

    /**
     * @var string|bool
     */
    protected $argument;

    /**
     * @var IHook
     */
    protected $action;

    /**
     * @var array
     */
    protected $locations = [
        'wp_enqueue_scripts' => 'front',
        'admin_enqueue_scripts' => 'admin',
        'login_enqueue_scripts' => 'login',
        'customize_preview_init' => 'customizer'
    ];

    public function __construct(AssetFileInterface $file, IHook $action)
    {
        $this->file = $file;
        $this->action = $action;
    }

    /**
     * Return the asset handle.
     *
     * @return string
     */
    public function getHandle(): string
    {
        return $this->handle;
    }

    /**
     * Set the asset handle.
     *
     * @param string $handle
     *
     * @return AssetInterface
     */
    public function setHandle(string $handle): AssetInterface
    {
        $this->handle = $handle;

        return $this;
    }

    /**
     * Return the asset file instance.
     *
     * @return AssetFileInterface
     */
    public function file(): AssetFileInterface
    {
        return $this->file;
    }

    /**
     * Return the asset path.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->file->getPath();
    }

    /**
     * Return the asset URL.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->file->getUrl();
    }

    /**
     * Set the asset dependencies.
     *
     * @param array|bool|string $dependencies
     *
     * @return AssetInterface
     */
    public function setDependencies($dependencies): AssetInterface
    {
        $this->dependencies = $dependencies;

        return $this;
    }

    /**
     * Return the asset dependencies.
     *
     * @return array|bool|mixed|string
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * Set the asset version.
     *
     * @param bool|null|string $version
     *
     * @return AssetInterface
     */
    public function setVersion($version): AssetInterface
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Return the asset version.
     *
     * @return null|string|bool
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set the asset type.
     * Override the auto-discovered type if any.
     *
     * @param string $type
     *
     * @return AssetInterface
     */
    public function setType(string $type): AssetInterface
    {
        $path = $this->file->isExternal() ? $this->getUrl() : $this->getPath();

        $this->file->setType($path, $type);

        return $this;
    }

    /**
     * Return the asset type.
     *
     * @return null|string
     */
    public function getType()
    {
        return $this->file->getType();
    }

    /**
     * Return the asset argument.
     *
     * @return bool|string
     */
    public function getArgument()
    {
        return $this->argument;
    }

    /**
     * Set the asset argument.
     *
     * @param bool|string $arg
     *
     * @return AssetInterface
     */
    public function setArgument($arg = null): AssetInterface
    {
        if (! is_null($arg)) {
            $this->argument = $arg;

            return $this;
        }

        // If no argument is passed but we have its type
        // then let's define some defaults.
        if ('style' === $this->getType()) {
            $this->argument = 'all';
        }

        if ('script' === $this->getType()) {
            $this->argument = true;
        }

        return $this;
    }

    /**
     * Load the asset on the defined area. Default to front-end.
     *
     * @param string|array $locations
     *
     * @return AssetInterface
     */
    public function to($locations = 'front'): AssetInterface
    {
        if (is_string($locations)) {
            $locations = [$locations];
        }

        foreach ($locations as $location) {
            $hook = array_search($location, $this->locations, true);

            if ($hook) {
                $this->install($hook);
            }
        }

        return $this;
    }

    /**
     * Register the asset with appropriate action hook.
     *
     * @param string $hook
     */
    protected function install(string $hook)
    {
        $this->action->add($hook, [$this, 'enqueue']);
    }

    /**
     * Enqueue asset.
     */
    public function enqueue()
    {
        if (is_null($this->getType())) {
            throw new AssetException('The asset must have a type defined. Null given.');
        }

        if ('script' === $this->getType()) {
            $this->enqueueScript();
        } else {
            $this->enqueueStyle();
        }
    }

    /**
     * Enqueue a script asset.
     */
    protected function enqueueScript()
    {
        wp_enqueue_script(
            $this->getHandle(),
            $this->getUrl(),
            $this->getDependencies(),
            $this->getVersion(),
            $this->getArgument()
        );
    }

    /**
     * Enqueue a style asset.
     */
    protected function enqueueStyle()
    {
        wp_enqueue_style(
            $this->getHandle(),
            $this->getUrl(),
            $this->getDependencies(),
            $this->getVersion(),
            $this->getArgument()
        );
    }
}
