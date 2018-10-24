<?php

namespace Themosis\Asset;

use Themosis\Hook\IHook;
use Themosis\Html\HtmlBuilder;

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

    /**
     * @var IHook
     */
    protected $filter;

    /**
     * @var HtmlBuilder
     */
    protected $html;

    public function __construct(Finder $finder, IHook $action, IHook $filter, HtmlBuilder $html)
    {
        $this->finder = $finder;
        $this->action = $action;
        $this->filter = $filter;
        $this->html = $html;
    }

    /**
     * Create and return an Asset instance.
     *
     * @param string           $handle
     * @param string           $path
     * @param array            $dependencies
     * @param null|string|bool $version
     * @param null|string|bool $arg
     *
     * @throws AssetException
     *
     * @return AssetInterface
     */
    public function add(string $handle, string $path, array $dependencies = [], $version = null, $arg = null)
    {
        if (empty($handle) || empty($path)) {
            throw new \InvalidArgumentException('The asset instance expects a handle name and a path or URL.');
        }

        return (new Asset($this->finder->find($path), $this->action, $this->filter, $this->html))
            ->setHandle($handle)
            ->setDependencies($dependencies)
            ->setVersion($version)
            ->setArgument($arg);
    }
}
