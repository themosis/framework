<?php

namespace Themosis\Asset;

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

    public function __construct(AssetFileInterface $file)
    {
        $this->file = $file;
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
}
