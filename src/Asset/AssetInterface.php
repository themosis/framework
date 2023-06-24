<?php

namespace Themosis\Asset;

interface AssetInterface
{
    /**
     * Return the asset handle.
     */
    public function getHandle(): string;

    /**
     * Set the asset handle.
     */
    public function setHandle(string $handle): AssetInterface;

    /**
     * Return the asset file instance.
     */
    public function file(): AssetFileInterface;

    /**
     * Return the asset path.
     */
    public function getPath(): string;

    /**
     * Return the asset URL.
     */
    public function getUrl(): string;

    /**
     * Set the asset dependencies.
     */
    public function setDependencies(array $dependencies): AssetInterface;

    /**
     * Return the asset dependencies.
     *
     * @return mixed
     */
    public function getDependencies();

    /**
     * Set the asset version.
     *
     * @param  null|string|bool  $version
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
     * @param  string|bool  $arg
     */
    public function setArgument($arg = null): AssetInterface;

    /**
     * Load the asset on defined area.
     *
     * @param  string|array  $locations
     */
    public function to($locations = 'front'): AssetInterface;

    /**
     * Localize the asset.
     */
    public function localize(string $name, array $data): AssetInterface;

    /**
     * Add asset inline code.
     */
    public function inline(string $code, bool $after = true): AssetInterface;

    /**
     * Add asset attributes.
     */
    public function attributes(array $attributes): AssetInterface;
}
