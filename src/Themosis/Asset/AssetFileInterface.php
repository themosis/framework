<?php

namespace Themosis\Asset;

interface AssetFileInterface
{
    /**
     * Check if the asset is external or local.
     *
     * @return bool
     */
    public function isExternal(): bool;

    /**
     * Set the external status of the file asset.
     *
     * @param bool $isExternal
     *
     * @return AssetFileInterface
     */
    public function setExternal(bool $isExternal = false): AssetFileInterface;

    /**
     * Return the asset file full path.
     *
     * @return string
     */
    public function getPath(): string;

    /**
     * Set the asset file path.
     *
     * @param string $path
     *
     * @return AssetFileInterface
     */
    public function setPath(string $path): AssetFileInterface;

    /**
     * Return the asset file URL.
     *
     * @return string
     */
    public function getUrl(): string;

    /**
     * Set the asset file URL.
     *
     * @param string $url
     *
     * @return AssetFileInterface
     */
    public function setUrl(string $url): AssetFileInterface;

    /**
     * Return the file type.
     *
     * @return null|string
     */
    public function getType();

    /**
     * Set the asset file type.
     *
     * @param string $filename
     * @param string $type
     *
     * @return AssetFileInterface
     */
    public function setType(string $filename, $type = null): AssetFileInterface;
}
