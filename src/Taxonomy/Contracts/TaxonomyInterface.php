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
}
