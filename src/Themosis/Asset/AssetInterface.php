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
}
