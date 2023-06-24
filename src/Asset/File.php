<?php

namespace Themosis\Asset;

use Illuminate\Filesystem\Filesystem;

class File implements AssetFileInterface
{
    protected $files;

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

    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $extensions = ['js', 'css', 'script', 'style'];

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * Check if the file is external or local.
     */
    public function isExternal(): bool
    {
        return $this->external;
    }

    /**
     * Set the asset file external status.
     */
    public function setExternal(bool $isExternal = false): AssetFileInterface
    {
        $this->external = $isExternal;

        return $this;
    }

    /**
     * Return the asset file path.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Set the asset file path.
     */
    public function setPath(string $path): AssetFileInterface
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Return the asset file URL.
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Set the asset file URL.
     */
    public function setUrl(string $url): AssetFileInterface
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Return the asset file type.
     *
     * @return null|string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the asset file type.
     *
     * @param  string  $type
     */
    public function setType(string $filename, $type = null): AssetFileInterface
    {
        if (! is_null($type) && in_array($type, $this->extensions, true)) {
            // We first listen to a defined asset type.
            // If no type is defined, let's try to discover it.
            $this->type = $this->findType($type);

            return $this;
        }

        $ext = $this->files->extension($filename);

        if (! empty($ext) && in_array($ext, $this->extensions, true)) {
            $this->type = $this->findType($ext);
        }

        return $this;
    }

    /**
     * Find the file type.
     *
     *
     * @return string
     */
    protected function findType(string $type)
    {
        return in_array($type, ['css', 'style'], true) ? 'style' : 'script';
    }
}
