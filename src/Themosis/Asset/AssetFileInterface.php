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
     * Return the asset file full path.
     *
     * @return string
     */
    public function getPath(): string;

    /**
     * Return the asset file URL.
     *
     * @return string
     */
    public function getUrl(): string;
}
