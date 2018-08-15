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
     * @param string            $handle
     * @param string            $path
     * @param bool|string|array $dependencies
     * @param null|string|bool  $version
     * @param null|string|bool  $arg
     *
     * @throws AssetException
     *
     * @return AssetInterface
     */
    public function add(string $handle, string $path, $dependencies = false, $version = null, $arg = null)
    {
        if (empty($handle) || empty($path)) {
            throw new \InvalidArgumentException('The asset instance expects a handle name and a path or URL.');
        }

        $file = $this->finder->find($path);

        return (new Asset($file))
            ->setHandle($handle)
            ->setDependencies($dependencies)
            ->setVersion($version)
            ->setArgument($arg);
    }
}
