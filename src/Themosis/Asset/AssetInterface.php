<?php

namespace Themosis\Asset;

interface AssetInterface
{
    /**
     * Return the asset handle.
     *
     * @return string
     */
    public function getHandle(): string;

    /**
     * Set the asset handle.
     *
     * @param string $handle
     *
     * @return AssetInterface
     */
    public function setHandle(string $handle): AssetInterface;

    /**
     * Return the asset file instance.
     *
     * @return AssetFileInterface
     */
    public function file(): AssetFileInterface;

    /**
     * Return the asset path.
     *
     * @return string
     */
    public function getPath(): string;

    /**
     * Return the asset URL.
     *
     * @return string
     */
    public function getUrl(): string;

    /**
     * Set the asset dependencies.
     *
     * @param bool|string|array $dependencies
     *
     * @return AssetInterface
     */
    public function setDependencies($dependencies): AssetInterface;

    /**
     * Return the asset dependencies.
     *
     * @return mixed
     */
    public function getDependencies();

    /**
     * Set the asset version.
     *
     * @param null|string|bool $version
     *
     * @return AssetInterface
     */
    public function setVersion($version): AssetInterface;

    /**
     * Return the asset version.
     *
     * @return mixed
     */
    public function getVersion();

    /**
     * Return the asset type.
     *
     * @return mixed
     */
    public function getType();

    /**
     * Set the asset type.
     *
     * @param string $type
     *
     * @return AssetInterface
     */
    public function setType(string $type): AssetInterface;

    /**
     * Get the asset argument.
     *
     * @return string|bool
     */
    public function getArgument();

    /**
     * Set the asset argument.
     *
     * @param string|bool $arg
     *
     * @return AssetInterface
     */
    public function setArgument($arg = null): AssetInterface;

    /**
     * Load the asset on defined area.
     *
     * @param string|array $locations
     *
     * @return AssetInterface
     */
    public function to($locations = 'front'): AssetInterface;
}
