<?php

namespace Themosis\Asset;

class File implements AssetFileInterface
{
    /**
     * @var bool
     */
    protected $external = false;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $url;

    public function __construct(string $path, string $url, bool $external = false)
    {
        $this->path = $path;
        $this->url = $url;
        $this->external = $external;
    }

    /**
     * Check if the file is external or local.
     *
     * @return bool
     */
    public function isExternal(): bool
    {
        return $this->external;
    }

    /**
     * Return the asset file path.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Return the asset file URL.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }
}
