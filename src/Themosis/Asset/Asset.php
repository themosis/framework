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
}
