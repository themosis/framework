<?php

namespace Themosis\PostType;

use Themosis\Core\Application;
use Themosis\Hook\IHook;
use Themosis\PostType\Contracts\PostTypeInterface;

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

    /**
     * @var IHook
     */
    protected $filter;

    public function __construct(Application $container, IHook $action, IHook $filter)
    {
        $this->container = $container;
        $this->action = $action;
        $this->filter = $filter;
    }

    /**
     * Create a new post type instance.
     *
     * @param string $slug
     * @param string $plural
     * @param string $singular
     *
     * @throws PostTypeException
     *
     * @return PostTypeInterface
     */
    public function make(string $slug, string $plural, string $singular): PostTypeInterface
    {
        $postType = (new PostType($slug, $this->action, $this->filter))
            ->setLabels(
                [
                    'name'                  => $plural,
                    'singular_name'         => $singular,
                    'add_new_item'          => sprintf('Add New %s', $singular),
                    'edit_item'             => sprintf('Edit %s', $singular),
                    'new_item'              => sprintf('New %s', $singular),
                    'view_item'             => sprintf('View %s', $singular),
                    'view_items'            => sprintf('View %s', $plural),
                    'search_items'          => sprintf('Search %s', $plural),
                    'not_found'             => sprintf('No %s found', $plural),
                    'not_found_in_trash'    => sprintf('No %s found in Trash', $plural),
                    'parent_item_colon'     => sprintf('Parent %s:', $singular),
                    'all_items'             => sprintf('All %s', $plural),
                    'archives'              => sprintf('%s Archives', $singular),
                    'attributes'            => sprintf('%s Attributes', $singular),
                    'insert_into_item'      => sprintf('Insert into %s', strtolower($singular)),
                    'uploaded_to_this_item' => sprintf('Uploaded to this %s', strtolower($singular)),
                    'filter_items_list'     => sprintf('Filter %s list', strtolower($plural)),
                    'items_list_navigation' => sprintf('%s list navigation', $plural),
                    'items_list'            => sprintf('%s list', $plural),
                ]
            )
            ->setArguments(
                [
                    'public'        => true,
                    'show_in_rest'  => true,
                    'menu_position' => 20,
                    'supports'      => ['title'],
                    'has_archive'   => true,
                ]
            );

        $abstract = sprintf('themosis.posttype.%s', $slug);

        if (! $this->container->bound($abstract)) {
            $this->container->instance($abstract, $postType);
        } else {
            throw new PostTypeException('The post type ['.$slug.'] already exists.');
        }

        return $postType;
    }

    /**
     * If a given post type exists
     *
     * @param string $slug
     *
     * @return bool
     */
    public function exists(string $slug): bool
    {
        return $this->container->has("themosis.posttype.{$slug}")
            || (function_exists('post_type_exists') && post_type_exists($slug));
    }

    /**
     * @param string $slug
     *
     * @return PostTypeInterface|null
     */
    public function get(string $slug): ?PostTypeInterface
    {
        $id = "themosis.posttype.{$slug}";

        if (! $this->container->has($id)) {
            return null;
        }

        return $this->container->get($id);
    }
}
