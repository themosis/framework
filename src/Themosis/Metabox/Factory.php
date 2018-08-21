<?php

namespace Themosis\Metabox;

use Themosis\Core\Application;
use Themosis\Forms\Contracts\FieldsRepositoryInterface;
use Themosis\Hook\IHook;
use Themosis\Metabox\Resources\MetaboxResourceInterface;

class Factory
{
    /**
     * @var Application
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

    /**
     * @var FieldsRepositoryInterface
     */
    protected $repository;

    public function __construct(
        Application $container,
        IHook $action,
        MetaboxResourceInterface $resource,
        FieldsRepositoryInterface $repository
    ) {
        $this->container = $container;
        $this->action = $action;
        $this->resource = $resource;
        $this->repository = $repository;
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
        return (new Metabox($id, $this->action, $this->repository))
            ->setContainer($this->container)
            ->setTitle($this->setDefaultTitle($id))
            ->setScreen($screen)
            ->setContext('advanced')
            ->setPriority('default')
            ->setResource($this->resource)
            ->setLocale($this->container->getLocale());
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
