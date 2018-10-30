<?php

namespace Themosis\Taxonomy;

use Themosis\Core\Application;
use Themosis\Taxonomy\Contracts\TaxonomyInterface;

class Factory
{
    /**
     * @var Application
     */
    protected $container;

    public function __construct(Application $container)
    {
        $this->container = $container;
    }

    /**
     * Register a taxonomy.
     *
     * @param string       $slug
     * @param string|array $objects
     *
     * @throws TaxonomyException
     *
     * @return TaxonomyInterface
     */
    public function make(string $slug, $objects, string $plural, string $singular): TaxonomyInterface
    {
        $taxonomy = (new Taxonomy());

        $abstract = sprintf('themosis.taxonomy.%s', $slug);

        if (! $this->container->bound($abstract)) {
            $this->container->instance($abstract, $taxonomy);
        } else {
            throw new TaxonomyException('The taxonomy ['.$slug.'] already exists.');
        }

        return $taxonomy;
    }
}
