<?php

namespace Themosis\Taxonomy\Contracts;

interface TaxonomyInterface
{
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
     * @param array $sargs
     * @return TaxonomyInterface
     */
    public function setArguments(array $sargs): TaxonomyInterface;

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
     * @return mixed
     */
    public function getArgument(string $property);
}
