<?php

namespace Themosis\Asset;

class AssetFinder
{
    /**
     * Asset directories paths & urls.
     *
     * @var array
     */
    protected $paths;

    /**
     * List of found assets.
     * $key is the asset relative path.
     * $value is the asset full URL.
     *
     * @var array
     */
    protected $assets = [];

    /**
     * List of URL schemes.
     *
     * @var array
     */
    protected $schemes = ['//', 'http://', 'https://'];

    /**
     * Add a path to look for assets.
     *
     * @param string $url The folder URL.
     * @param string $path The folder path.
     *
     * @return $this
     */
    public function addPath($url, $path)
    {
        if (!isset($this->paths[$url])) {
            $this->paths[$url] = $path;
        }

        return $this;
    }

    /**
     * Add paths to look for assets.
     *
     * @param array $paths
     *
     * @throws AssetException
     *
     * @return $this
     */
    public function addPaths(array $paths)
    {
        foreach ($paths as $url => $path) {
            if (!is_numeric($url)) {
                $this->addPath($url, $path);
            } else {
                throw new AssetException('Please provide an URL as key and a PATH as value in order to find assets.');
            }
        }

        return $this;
    }

    /**
     * Get the full URL path of an asset.
     *
     * @param string $path
     *
     * @return string
     */
    public function find($path)
    {
        // Check if asset is external.
        if ($this->isExternal($path)) {
            return $this->assets[$path] = $path;
        }

        // Check if asset is already registered.
        if (isset($this->assets[$path])) {
            return $this->assets[$path];
        }

        // Find and register the asset.
        return $this->assets[$path] = $this->findInPaths($path, $this->paths);
    }

    /**
     * Look for an asset file in all registered directories.
     *
     * @param string $path The relative path.
     * @param array  $dirs Registered asset directories.
     *
     * @throws AssetException
     *
     * @return string
     */
    protected function findInPaths($path, array $dirs)
    {
        $path = $this->parsePath($path);

        foreach ($dirs as $dirUrl => $dirPath) {
            if (file_exists($dirPath.$path)) {
                // Return the full URL.
                return $dirUrl.$path;
            }
        }

        throw new AssetException("Asset with path: {$path} not found.");
    }

    /**
     * Check if a path is external or not.
     *
     * @param string $path
     *
     * @return bool
     */
    protected function isExternal($path)
    {
        foreach ($this->schemes as $scheme) {
            if (strpos($path, $scheme) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sanitized the given asset path and check if there
     * is a '/' symbol at the beginning of the path.
     *
     * @param string $path
     *
     * @return string
     */
    protected function parsePath($path)
    {
        if (substr($path, 0, 1) !== '/' && substr($path, 0, 4) !== 'http') {
            return '/'.$path;
        }

        return $path;
    }
}
