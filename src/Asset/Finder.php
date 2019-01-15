<?php

namespace Themosis\Asset;

use Illuminate\Filesystem\Filesystem;

class Finder
{
    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var array
     */
    protected $locations = [];

    /**
     * @var array
     */
    protected $schemes = ['//', 'http'];

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * Add a base location in order to find an asset.
     *
     * @param string $path
     * @param string $url
     *
     * @return Finder
     */
    public function addLocation(string $path, string $url): Finder
    {
        $path = rtrim($path, '\/');
        $url = rtrim($url, '\/');

        $this->locations[$path] = $url;

        return $this;
    }

    /**
     * Add multiple locations.
     * The key is the asset path and the value its URL.
     *
     * @param array $paths
     *
     * @return Finder
     */
    public function addLocations(array $paths): Finder
    {
        foreach ($paths as $path => $url) {
            $this->addLocation($path, $url);
        }

        return $this;
    }

    /**
     * Return the registered locations.
     *
     * @return array
     */
    public function getLocations(): array
    {
        return $this->locations;
    }

    /**
     * Return an asset file instance if found.
     *
     * @param string $path
     *
     * @throws AssetException
     *
     * @return AssetFileInterface
     */
    public function find(string $path): AssetFileInterface
    {
        if ($this->isExternal($path)) {
            return (new File($this->files))
                ->setPath('')
                ->setUrl($path)
                ->setExternal(true)
                ->setType($path);
        }

        $path = trim($path, '\/');

        foreach ($this->locations as $dir => $url) {
            if ($this->files->exists($fullPath = $dir.'/'.$path)) {
                return (new File($this->files))
                    ->setPath($fullPath)
                    ->setUrl($url.'/'.$path)
                    ->setExternal(false)
                    ->setType($fullPath);
            }
        }

        throw new AssetException('Unable to find the asset with the following path: '.$path);
    }

    /**
     * Check if given path is an external asset or not.
     *
     * @param string $path
     *
     * @return bool
     */
    protected function isExternal(string $path): bool
    {
        foreach ($this->schemes as $scheme) {
            if (strpos($path, $scheme) !== false) {
                return true;
            }
        }

        return false;
    }
}
