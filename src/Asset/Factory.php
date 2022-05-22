<?php

namespace Themosis\Asset;

use Themosis\Hook\IHook;
use Themosis\Html\HtmlBuilder;

class Factory
{
    protected Finder $finder;

    protected IHook $action;

    protected IHook $filter;

    protected HtmlBuilder $html;

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
     * @throws AssetException
     */
    public function add(string $handle, string $path, array $dependencies = [], string|bool|null $version = null, string|bool|null $arg = null): AssetInterface
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
