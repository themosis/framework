<?php

namespace Themosis\PostType\Contracts;

interface PostTypeInterface
{
    /**
     * Set the post type labels.
     */
    public function setLabels(array $labels): PostTypeInterface;

    /**
     * Return the post type labels.
     */
    public function getLabels(): array;

    /**
     * Return a defined label value.
     */
    public function getLabel(string $name): string;

    /**
     * Set the post type arguments.
     */
    public function setArguments(array $args): PostTypeInterface;

    /**
     * Return the post type arguments.
     */
    public function getArguments(): array;

    /**
     * Return a post type argument.
     *
     *
     * @return mixed
     */
    public function getArgument(string $property);

    /**
     * Return the WordPress WP_Post_Type instance.
     *
     * @return \WP_Post_Type|null
     */
    public function getInstance();

    /**
     * Register the post type.
     */
    public function set(): PostTypeInterface;

    /**
     * Set the post type title input placeholder.
     */
    public function setTitlePlaceholder(string $title): PostTypeInterface;

    /**
     * Set post type custom status.
     *
     * @param  array|string  $status
     */
    public function status($status, array $args = []): PostTypeInterface;

    /**
     * Check if post type has custom status.
     */
    public function hasStatus(): bool;

    /**
     * Return the post type slug.
     */
    public function getSlug(): string;

    /**
     * Return the post type slug.
     * Aliased method for getSlug.
     */
    public function getName(): string;
}
