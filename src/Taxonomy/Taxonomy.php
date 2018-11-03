<?php

namespace Themosis\Taxonomy;

use Illuminate\Contracts\Container\Container;
use Themosis\Hook\IHook;
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
    protected $args = [];

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var IHook
     */
    protected $action;

    public function __construct(string $slug, array $objects, Container $container, IHook $action)
    {
        $this->slug = $slug;
        $this->objects = $objects;
        $this->container = $container;
        $this->action = $action;

        $this->parseObjectsForCustomStatus($this->objects);
    }

    /**
     * Return the taxonomy slug.
     *
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * Return the taxonomy slug.
     * Aliased method for getSlug.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->getSlug();
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
     * @param array $args
     *
     * @return TaxonomyInterface
     */
    public function setArguments(array $args): TaxonomyInterface
    {
        $this->args = array_merge($this->args, $args);

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

    /**
     * Register the taxonomy.
     *
     * @return TaxonomyInterface
     */
    public function set(): TaxonomyInterface
    {
        if (function_exists('current_filter') && 'init' === $hook = current_filter()) {
            $this->register();
        } else {
            $this->action->add('init', [$this, 'register']);
        }

        return $this;
    }

    /**
     * Register taxonomy hook callback.
     */
    public function register()
    {
        register_taxonomy($this->slug, $this->objects, $this->getArguments());
        $this->bind();
    }

    /**
     * Bind the taxonomy to its custom post type|object. Make sure the taxonomy
     * can be found in 'parse_query' or 'pre_get_posts' filters.
     */
    protected function bind()
    {
        foreach ($this->objects as $object) {
            register_taxonomy_for_object_type($this->slug, $object);
        }
    }

    /**
     * Set taxonomy objects.
     *
     * @param array|string $objects
     *
     * @return TaxonomyInterface
     */
    public function setObjects($objects): TaxonomyInterface
    {
        $this->objects = array_unique(array_merge($this->objects, (array) $objects));

        // Check if object have custom post status.
        // If so, change its default update count callback function.
        $this->parseObjectsForCustomStatus($this->objects);

        return $this;
    }

    /**
     * Parse attached objects and set default update count callback.
     *
     * @param array $objects
     *
     * @return TaxonomyInterface
     */
    protected function parseObjectsForCustomStatus(array $objects): TaxonomyInterface
    {
        foreach ($objects as $object) {
            if ($this->container->bound('themosis.posttype.'.$object)) {
                $postType = $this->container['themosis.posttype.'.$object];

                if ($postType->hasStatus()) {
                    $this->setArguments([
                        'update_count_callback' => '_update_generic_term_count'
                    ]);
                    break;
                }
            }
        }

        return $this;
    }

    /**
     * Return taxonomy attached objects.
     *
     * @return array
     */
    public function getObjects(): array
    {
        return $this->objects;
    }
}
