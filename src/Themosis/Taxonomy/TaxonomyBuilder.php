<?php
namespace Themosis\Taxonomy;

use Themosis\Action\Action;
use Themosis\Core\DataContainer;
use Themosis\Core\Wrapper;

class TaxonomyBuilder extends Wrapper
{

    /**
     * Store the taxonomy data.
     *
     * @var DataContainer
     */
    protected $datas;

    /**
     * The 'init' event.
     */
    protected $event;

    /**
     * Build a TaxonomyBuilder instance.
     *
     * @param DataContainer $datas The taxonomy properties.
     */
    public function __construct(DataContainer $datas)
    {
        $this->datas = $datas;
        $this->event = Action::listen('init', $this, 'register');
    }

    /**
     * @param string $slug The taxonomy slug name.
     * @param string|array $postType The taxonomy object type slug: 'post', 'page', ...
     * @param string $plural The taxonomy plural display name.
     * @param string $singular The taxonomy singular display name.
     * @throws TaxonomyException
     * @return \Themosis\Taxonomy\TaxonomyBuilder
     */
    public function make($slug, $postType, $plural, $singular)
    {
        $params = compact('slug', 'postType', 'plural', 'singular');

        foreach ($params as $name => $param) {
            if ('postType' !== $name && !is_string($param)) {
                throw new TaxonomyException('Invalid taxonomy parameter "' . $name . '"');
            }
        }

        // Store properties.
        $this->datas['slug'] = $slug;
        $this->datas['postType'] = (array) $postType;
        $this->datas['args'] = $this->setDefaultArguments($plural, $singular);

        return $this;
    }

    /**
     * Set the custom taxonomy. A user can also override the
     * arguments by passing an array of taxonomy arguments.
     *
     * @link http://codex.wordpress.org/Function_Reference/register_taxonomy
     * @param array $params Taxonomy arguments to override defaults.
     * @return \Themosis\Taxonomy\TaxonomyBuilder
     */
    public function set(array $params = [])
    {
        // Override custom taxonomy arguments if given.
        $this->datas['args'] = array_merge($this->datas['args'], $params);

        // Trigger the 'init' event in order to register the custom taxonomy.
        // Check if we are not already called by a method attached to the `init` hook.
        $current = current_filter();

        if ('init' === $current) {
            // If inside an `init` action, simply call the register method.
            $this->register();
        } else {
            // Out of an `init` action, call the hook.
            $this->event->dispatch();
        }

        return $this;
    }
    /**
     * @param null $property
     * @return array
     * @throws TaxonomyException
     */
    public function get($property = null)
    {
        $args = [
            'slug' => $this->datas['slug'],
            'post_type' => $this->datas['postType'],
        ];
        $properties = array_merge($args, $this->datas['args']);

        // If no property asked, return all defined properties.
        if (is_null($property) || empty($property)) {
            return $properties;
        }

        // If property exists, return it.
        if (isset($properties[$property])) {
            return $properties[$property];
        }

        throw new TaxonomyException("Property '{$property}' does not exist on the '{$properties['label']}' taxonomy.");
    }

    /**
     * Triggered by the 'init' action/event.
     * Register the custom taxonomy.
     *
     * @return void
     */
    public function register()
    {
        register_taxonomy($this->datas['slug'], $this->datas['postType'], $this->datas['args']);
    }

    /**
     * Link the taxonomy to its custom post type. Allow the taxonomy
     * to be found in 'parse_query' or 'pre_get_posts' filters.
     *
     * @link http://codex.wordpress.org/Function_Reference/register_taxonomy_for_object_type
     * @return \Themosis\Taxonomy\TaxonomyBuilder
     */
    public function bind()
    {
        foreach ($this->datas['postType'] as $objectType) {
            register_taxonomy_for_object_type($this->datas['slug'], $objectType);
        }

        return $this;
    }

    /**
     * Set the taxonomy default arguments.
     *
     * @param string $plural The plural display name.
     * @param string $singular The singular display name.
     * @return array
     */
    protected function setDefaultArguments($plural, $singular)
    {
        $labels = [
            'name' => _x($plural, THEMOSIS_FRAMEWORK_TEXTDOMAIN),
            'singular_name' => _x($singular, THEMOSIS_FRAMEWORK_TEXTDOMAIN),
            'search_items' => __('Search ' . $plural, THEMOSIS_FRAMEWORK_TEXTDOMAIN),
            'all_items' => __('All ' . $plural, THEMOSIS_FRAMEWORK_TEXTDOMAIN),
            'parent_item' => __('Parent ' . $singular, THEMOSIS_FRAMEWORK_TEXTDOMAIN),
            'parent_item_colon' => __('Parent ' . $singular . ': ', THEMOSIS_FRAMEWORK_TEXTDOMAIN),
            'edit_item' => __('Edit ' . $singular, THEMOSIS_FRAMEWORK_TEXTDOMAIN),
            'update_item' => __('Update ' . $singular, THEMOSIS_FRAMEWORK_TEXTDOMAIN),
            'add_new_item' => __('Add New ' . $singular, THEMOSIS_FRAMEWORK_TEXTDOMAIN),
            'new_item_name' => __('New ' . $singular . ' Name', THEMOSIS_FRAMEWORK_TEXTDOMAIN),
            'menu_name' => __($plural, THEMOSIS_FRAMEWORK_TEXTDOMAIN),
        ];

        $defaults = [
            'label' => __($plural, THEMOSIS_FRAMEWORK_TEXTDOMAIN),
            'labels' => $labels,
            'public' => true,
            'query_var' => true,
        ];

        return $defaults;
    }

}
