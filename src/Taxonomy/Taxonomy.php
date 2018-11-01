<?php

namespace Themosis\Taxonomy;

use Themosis\Taxonomy\Contracts\TaxonomyInterface;

class Taxonomy implements TaxonomyInterface
{
    /**
     * @var string
     */
    protected $slug;

    /**
     * @var array
     */
    protected $objects;

    /**
     * @var array
     */
    protected $args;

    public function __construct(string $slug, array $objects)
    {
        $this->slug = $slug;
        $this->objects = $objects;
    }

    /**
     * Set taxonomy labels.
     *
     * @param array $labels
     *
     * @return TaxonomyInterface
     */
    public function setLabels(array $labels): TaxonomyInterface
    {
        if (isset($this->args['labels'])) {
            $this->args['labels'] = array_merge($this->args['labels'], $labels);
        } else {
            $this->args['labels'] = $labels;
        }

        return $this;
    }

    /**
     * Return taxonomy labels.
     *
     * @return array
     */
    public function getLabels(): array
    {
        return $this->args['labels'] ?? [];
    }

    /**
     * Return a taxonomy label by name.
     *
     * @param string $name
     *
     * @return string
     */
    public function getLabel(string $name): string
    {
        $labels = $this->getLabels();

        return $labels[$name] ?? '';
    }

    /**
     * Set taxonomy arguments.
     *
     * @param array $sargs
     * @return TaxonomyInterface
     */
    public function setArguments(array $sargs): TaxonomyInterface
    {
        $this->args = array_merge($this->args, $sargs);

        return $this;
    }

    /**
     * Return taxonomy arguments.
     *
     * @return array
     */
    public function getArguments(): array
    {
        return $this->args;
    }

    /**
     * Return a taxonomy argument.
     *
     * @param string $property
     *
     * @return mixed
     */
    public function getArgument(string $property)
    {
        $args = $this->getArguments();

        return $args[$property] ?? null;
    }
}
