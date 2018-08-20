<?php

namespace Themosis\Metabox;

use Illuminate\Contracts\Container\Container;
use Themosis\Hook\IHook;
use Themosis\Metabox\Resources\MetaboxResourceInterface;

class Factory
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var IHook
     */
    protected $action;

    /**
     * @var MetaboxResourceInterface
     */
    protected $resource;

    public function __construct(Container $container, IHook $action, MetaboxResourceInterface $resource)
    {
        $this->container = $container;
        $this->action = $action;
        $this->resource = $resource;
    }

    /**
     * Create a new metabox instance.
     *
     * @param string                  $id
     * @param string|array|\WP_Screen $screen
     *
     * @return MetaboxInterface
     */
    public function make(string $id, $screen = 'post'): MetaboxInterface
    {
        return (new Metabox($id, $this->action))
            ->setContainer($this->container)
            ->setTitle($this->setDefaultTitle($id))
            ->setScreen($screen)
            ->setContext('advanced')
            ->setPriority('default')
            ->setResource($this->resource);
    }

    /**
     * Format a default title based on given name.
     *
     * @param string $name
     *
     * @return string
     */
    protected function setDefaultTitle(string $name): string
    {
        return ucfirst(str_replace(['_', '-', '.'], ' ', $name));
    }
}
