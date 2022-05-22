<?php

namespace Themosis\Asset;

interface AssetFileInterface
{
    /**
     * Check if the asset is external or local.
     */
    public function isExternal(): bool;

    /**
     * Set the external status of the file asset.
     */
    public function setExternal(bool $isExternal = false): AssetFileInterface;

    /**
     * Return the asset file full path.
     */
    public function getPath(): string;

    /**
     * Set the asset file path.
     */
    public function setPath(string $path): AssetFileInterface;

    /**
     * Return the asset file URL.
     */
    public function getUrl(): string;

    /**
     * Set the asset file URL.
     */
    public function setUrl(string $url): AssetFileInterface;

    /**
     * Return the file type.
     */
    public function getType(): ?string;

    /**
     * Set the asset file type.
     */
    public function setType(string $filename, ?string $type = null): AssetFileInterface;
}
