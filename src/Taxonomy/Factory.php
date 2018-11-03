<?php

namespace Themosis\Taxonomy;

use Themosis\Core\Application;
use Themosis\Hook\IHook;
use Themosis\Taxonomy\Contracts\TaxonomyInterface;

class Factory
{
    /**
     * @var Application
     */
    protected $container;

    /**
     * @var IHook
     */
    protected $action;

    public function __construct(Application $container, IHook $action)
    {
        $this->container = $container;
        $this->action = $action;
    }

    /**
     * Register a taxonomy.
     *
     * @param string       $slug
     * @param string|array $objects
     * @param string       $plural
     * @param string       $singular
     *
     * @throws TaxonomyException
     *
     * @return TaxonomyInterface
     */
    public function make(string $slug, $objects, string $plural, string $singular): TaxonomyInterface
    {
        $taxonomy = (new Taxonomy($slug, (array) $objects, $this->container, $this->action))
            ->setLabels([
                'name' => $plural,
                'singular_name' => $singular,
                'search_items' => sprintf('Search %s', $plural),
                'popular_items' => sprintf('Popular %s', $plural),
                'all_items' => sprintf('All %s', $plural),
                'parent_item' => sprintf('Parent %s', $singular),
                'parent_item_colon' => sprintf('Parent %s:', $singular),
                'edit_item' => sprintf('Edit %s', $singular),
                'view_item' => sprintf('View %s', $singular),
                'update_item' => sprintf('Update %s', $singular),
                'add_new_item' => sprintf('Add New %s', $singular),
                'new_item_name' => sprintf('New %s Name', $singular),
                'separate_items_with_commas' => sprintf('Separate %s with commas', strtolower($plural)),
                'add_or_remove_items' => sprintf('Add or remove %s', strtolower($plural)),
                'choose_from_most_used' => sprintf('Choose from the most used %s', strtolower($plural)),
                'not_found' => sprintf('No %s found', strtolower($plural)),
                'no_terms' => sprintf('No %s', strtolower($plural)),
                'items_list_navigation' => sprintf('%s list navigation', $plural),
                'items_list' => sprintf('%s list', $plural),
                'most_used' => 'Most Used',
                'back_to_items' => sprintf('Back to %s', $plural)
            ])
            ->setArguments([
                'public' => true,
                'show_in_rest' => true,
                'show_admin_column' => true
            ]);

        $abstract = sprintf('themosis.taxonomy.%s', $slug);

        if (! $this->container->bound($abstract)) {
            $this->container->instance($abstract, $taxonomy);
        } else {
            throw new TaxonomyException('The taxonomy ['.$slug.'] already exists.');
        }

        return $taxonomy;
    }
}
