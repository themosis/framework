<?php

namespace Themosis\Asset;

class Factory
{
    /**
     * @var Finder
     */
    protected $finder;

    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * Create and return an Asset instance.
     *
     * @param string $handle
     * @param string $path
     *
     * @throws AssetException
     *
     * @return Asset
     */
    public function add(string $handle, string $path)
    {
        if (empty($handle) || empty($path)) {
            throw new \InvalidArgumentException('The asset instance expects a handle name and a path or URL.');
        }

        $file = $this->finder->find($path);

        $asset = new Asset($file);
        $asset->setHandle($handle);

        return $asset;
    }
}
