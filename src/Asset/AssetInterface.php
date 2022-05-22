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
     */
    public function getDependencies(): string|array|bool;

    /**
     * Set the asset version.
     */
    public function setVersion(string|bool|null $version): AssetInterface;

    /**
     * Return the asset version.
     */
    public function getVersion(): string|bool|null;

    /**
     * Return the asset type.
     */
    public function getType(): string|null;

    /**
     * Set the asset type.
     */
    public function setType(string $type): AssetInterface;

    /**
     * Get the asset argument.
     */
    public function getArgument(): string|bool;

    /**
     * Set the asset argument.
     */
    public function setArgument(string|bool|null $arg = null): AssetInterface;

    /**
     * Load the asset on defined area.
     */
    public function to(string|array $locations = 'front'): AssetInterface;

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
     *
     * @param array $attributes
     *
     * @return AssetInterface
     */
    public function attributes(array $attributes): AssetInterface;
}
