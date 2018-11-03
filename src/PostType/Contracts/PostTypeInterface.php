<?php

namespace Themosis\PostType\Contracts;

interface PostTypeInterface
{
    /**
     * Set the post type labels.
     *
     * @param array $labels
     *
     * @return PostTypeInterface
     */
    public function setLabels(array $labels): PostTypeInterface;

    /**
     * Return the post type labels.
     *
     * @return array
     */
    public function getLabels(): array;

    /**
     * Return a defined label value.
     *
     * @param string $name
     *
     * @return string
     */
    public function getLabel(string $name): string;

    /**
     * Set the post type arguments.
     *
     * @param array $args
     *
     * @return PostTypeInterface
     */
    public function setArguments(array $args): PostTypeInterface;

    /**
     * Return the post type arguments.
     *
     * @return array
     */
    public function getArguments(): array;

    /**
     * Return a post type argument.
     *
     * @param string $property
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
     *
     * @return PostTypeInterface
     */
    public function set(): PostTypeInterface;

    /**
     * Set the post type title input placeholder.
     *
     * @param string $title
     *
     * @return PostTypeInterface
     */
    public function setTitlePlaceholder(string $title): PostTypeInterface;

    /**
     * Set post type custom status.
     *
     * @param array|string $status
     * @param array        $args
     *
     * @return PostTypeInterface
     */
    public function status($status, array $args = []): PostTypeInterface;

    /**
     * Check if post type has custom status.
     *
     * @return bool
     */
    public function hasStatus(): bool;

    /**
     * Return the post type slug.
     *
     * @return string
     */
    public function getSlug(): string;

    /**
     * Return the post type slug.
     * Aliased method for getSlug.
     *
     * @return string
     */
    public function getName(): string;
}
