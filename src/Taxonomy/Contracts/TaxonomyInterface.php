<?php

namespace Themosis\Taxonomy\Contracts;

interface TaxonomyInterface
{
    /**
     * Return the taxonomy slug.
     */
    public function getSlug(): string;

    /**
     * Return the taxonomy slug.
     * Aliased method for getSlug.
     */
    public function getName(): string;

    /**
     * Set taxonomy labels.
     */
    public function setLabels(array $labels): TaxonomyInterface;

    /**
     * Return taxonomy labels.
     */
    public function getLabels(): array;

    /**
     * Return a taxonomy label by name.
     */
    public function getLabel(string $name): string;

    /**
     * Set taxonomy arguments.
     */
    public function setArguments(array $args): TaxonomyInterface;

    /**
     * Return taxonomy arguments.
     */
    public function getArguments(): array;

    /**
     * Return a taxonomy argument.
     *
     *
     * @return mixed
     */
    public function getArgument(string $property);

    /**
     * Register the taxonomy.
     */
    public function set(): TaxonomyInterface;

    /**
     * Set taxonomy objects.
     *
     * @param  string|array  $objects
     */
    public function setObjects($objects): TaxonomyInterface;

    /**
     * Return taxonomy attached objects.
     */
    public function getObjects(): array;
}
