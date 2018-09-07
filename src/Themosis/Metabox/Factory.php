<?php

namespace Themosis\Metabox;

use Themosis\Core\Application;
use Themosis\Forms\Fields\FieldsRepository;
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
     * @var IHook
     */
    protected $filter;

    /**
     * @var MetaboxResourceInterface
     */
    protected $resource;

    public function __construct(
        Application $container,
        IHook $action,
        IHook $filter,
        MetaboxResourceInterface $resource
    ) {
        $this->container = $container;
        $this->action = $action;
        $this->filter = $filter;
        $this->resource = $resource;
    }

    /**
     * Create a new metabox instance.
     *
     * @param string                  $id
     * @param string|array|\WP_Screen $screen
     *
     * @throws MetaboxException
     *
     * @return MetaboxInterface
     */
    public function make(string $id, $screen = 'post'): MetaboxInterface
    {
        $metabox = (new Metabox($id, $this->action, $this->filter, new FieldsRepository()))
            ->setContainer($this->container)
            ->setTitle($this->setDefaultTitle($id))
            ->setScreen($screen)
            ->setContext('advanced')
            ->setPriority('default')
            ->setResource($this->resource)
            ->setLocale($this->container->getLocale());

        $abstract = sprintf('themosis.metabox.%s', $id);

        if (! $this->container->bound($abstract)) {
            $this->container->instance($abstract, $metabox);
        } else {
            throw new MetaboxException('The metabox with an ID of ['.$id.'] is already bound.');
        }

        return $metabox;
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
