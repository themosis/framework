<?php

namespace Themosis\Taxonomy\Contracts;

interface TaxonomyInterface
{
    /**
     * Return the taxonomy slug.
     *
     * @return string
     */
    public function getSlug(): string;

    /**
     * Return the taxonomy slug.
     * Aliased method for getSlug.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Set taxonomy labels.
     *
     * @param array $labels
     *
     * @return TaxonomyInterface
     */
    public function setLabels(array $labels): TaxonomyInterface;

    /**
     * Return taxonomy labels.
     *
     * @return array
     */
    public function getLabels(): array;

    /**
     * Return a taxonomy label by name.
     *
     * @param string $name
     *
     * @return string
     */
    public function getLabel(string $name): string;

    /**
     * Set taxonomy arguments.
     *
     * @param array $args
     *
     * @return TaxonomyInterface
     */
    public function setArguments(array $args): TaxonomyInterface;

    /**
     * Return taxonomy arguments.
     *
     * @return array
     */
    public function getArguments(): array;

    /**
     * Return a taxonomy argument.
     *
     * @param string $property
     *
     * @return mixed
     */
    public function getArgument(string $property);

    /**
     * Register the taxonomy.
     *
     * @return TaxonomyInterface
     */
    public function set(): TaxonomyInterface;

    /**
     * Set taxonomy objects.
     *
     * @param string|array $objects
     *
     * @return TaxonomyInterface
     */
    public function setObjects($objects): TaxonomyInterface;

    /**
     * Return taxonomy attached objects.
     *
     * @return array
     */
    public function getObjects(): array;
}
