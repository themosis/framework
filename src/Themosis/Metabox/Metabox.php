<?php

namespace Themosis\Metabox;

class Metabox implements MetaboxInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string|array|\WP_Screen
     */
    protected $screen;

    /**
     * @var string
     */
    protected $context;

    /**
     * @var string
     */
    protected $priority;

    /**
     * @var string|callable
     */
    protected $callback;

    /**
     * @var array
     */
    protected $args;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * Return the metabox id.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Set the metabox title.
     *
     * @param string $title
     *
     * @return MetaboxInterface
     */
    public function setTitle(string $title): MetaboxInterface
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Return the metabox title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set the metabox screen.
     *
     * @param string|array|\WP_Screen $screen
     *
     * @return MetaboxInterface
     */
    public function setScreen($screen): MetaboxInterface
    {
        $this->screen = $screen;

        return $this;
    }

    /**
     * Return the metabox screen.
     *
     * @return array|string|\WP_Screen
     */
    public function getScreen()
    {
        return $this->screen;
    }

    /**
     * Set the metabox context.
     *
     * @param string $context
     *
     * @return MetaboxInterface
     */
    public function setContext(string $context): MetaboxInterface
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Return the metabox context.
     *
     * @return string
     */
    public function getContext(): string
    {
        return $this->context;
    }

    /**
     * Set the metabox priority.
     *
     * @param string $priority
     *
     * @return MetaboxInterface
     */
    public function setPriority(string $priority): MetaboxInterface
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Return the metabox priority.
     *
     * @return string
     */
    public function getPriority(): string
    {
        return $this->priority;
    }

    /**
     * Set the metabox callback.
     *
     * @param callable|string $callback
     *
     * @return MetaboxInterface
     */
    public function setCallback($callback): MetaboxInterface
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * Set the metabox callback arguments.
     *
     * @param array $args
     *
     * @return MetaboxInterface
     */
    public function setArguments(array $args): MetaboxInterface
    {
        $this->args = $args;

        return $this;
    }
}
