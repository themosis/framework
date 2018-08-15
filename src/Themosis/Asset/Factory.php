<?php

namespace Themosis\Asset;

use Themosis\Hook\IHook;

class Factory
{
    /**
     * @var Finder
     */
    protected $finder;

    /**
     * @var IHook
     */
    protected $action;

    public function __construct(Finder $finder, IHook $action)
    {
        $this->finder = $finder;
        $this->action = $action;
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

        return (new Asset($this->finder->find($path), $this->action))
            ->setHandle($handle)
            ->setDependencies($dependencies)
            ->setVersion($version)
            ->setArgument($arg);
    }
}
