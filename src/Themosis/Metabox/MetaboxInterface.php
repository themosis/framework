<?php

namespace Themosis\Metabox;

interface MetaboxInterface
{
    /**
     * Return the metabox id.
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Set the metabox title.
     *
     * @param string $title
     *
     * @return MetaboxInterface
     */
    public function setTitle(string $title): MetaboxInterface;

    /**
     * Return the metabox title.
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Set the metabox screen.
     *
     * @param string|array|\WP_Screen
     * @param mixed $screen
     *
     * @return MetaboxInterface
     */
    public function setScreen($screen): MetaboxInterface;

    /**
     * Return the metabox screen.
     *
     * @return string|array|\WP_Screen
     */
    public function getScreen();

    /**
     * Set the metabox context.
     *
     * @param string $context
     *
     * @return MetaboxInterface
     */
    public function setContext(string $context): MetaboxInterface;

    /**
     * Get the metabox context.
     *
     * @return string
     */
    public function getContext(): string;

    /**
     * Set the metabox priority.
     *
     * @param string $priority
     *
     * @return MetaboxInterface
     */
    public function setPriority(string $priority): MetaboxInterface;

    /**
     * Get the metabox priority.
     *
     * @return string
     */
    public function getPriority(): string;

    /**
     * Set the metabox callback.
     *
     * @param string|callable $callback
     *
     * @return MetaboxInterface
     */
    public function setCallback($callback): MetaboxInterface;

    /**
     * Set the metabox controller arguments.
     *
     * @param array $args
     *
     * @return MetaboxInterface
     */
    public function setArguments(array $args): MetaboxInterface;
}
