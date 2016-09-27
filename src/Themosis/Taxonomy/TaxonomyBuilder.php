<?php

namespace Themosis\Taxonomy;

use Illuminate\View\Factory;
use Themosis\Foundation\Application;
use Themosis\Foundation\DataContainer;
use Themosis\Field\Wrapper;
use Themosis\Hook\IHook;
use Themosis\Validation\ValidationBuilder;

class TaxonomyBuilder extends Wrapper
{
    /**
     * Store the taxonomy data.
     *
     * @var DataContainer
     */
    protected $datas;

    /**
     * @var IHook
     */
    protected $action;

    /**
     * @var ValidationBuilder
     */
    protected $validator;

    /**
     * @var Factory
     */
    protected $view;

    /**
     * The taxonomy custom fields.
     * 
     * @var array
     */
    protected $fields = [];

    /**
     * @var Application
     */
    protected $container;

    /**
     * @var string
     */
    protected $prefix = 'taxonomy';

    /**
     * Build a TaxonomyBuilder instance.
     *
     * @param Application                            $container
     * @param DataContainer                          $datas     The taxonomy properties.
     * @param \Themosis\Hook\IHook                   $action
     * @param \Themosis\Validation\ValidationBuilder $validator
     * @param \Illuminate\View\Factory               $view
     */
    public function __construct(Application $container, DataContainer $datas, IHook $action, ValidationBuilder $validator, Factory $view)
    {
        $this->container = $container;
        $this->datas = $datas;
        $this->action = $action;
        $this->validator = $validator;
        $this->view = $view;
    }

    /**
     * @param string       $name     The taxonomy slug name.
     * @param string|array $postType The taxonomy object type slug: 'post', 'page', ...
     * @param string       $plural   The taxonomy plural display name.
     * @param string       $singular The taxonomy singular display name.
     *
     * @throws TaxonomyException
     *
     * @return \Themosis\Taxonomy\TaxonomyBuilder
     */
    public function make($name, $postType, $plural, $singular)
    {
        $params = compact('name', 'postType', 'plural', 'singular');

        foreach ($params as $key => $param) {
            if ('postType' !== $key && !is_string($param)) {
                throw new TaxonomyException('Invalid taxonomy parameter "'.$key.'"');
            }
        }

        // Convert post type names to an array (a taxonomy can be shared between multiple post types).
        $postType = (array) $postType;

        // Store properties.
        $this->datas['name'] = $name;
        $this->datas['postType'] = $postType;
        $this->datas['args'] = $this->setDefaultArguments($postType, $plural, $singular);

        return $this;
    }

    /**
     * Set the custom taxonomy. A user can also override the
     * arguments by passing an array of taxonomy arguments.
     *
     * @link http://codex.wordpress.org/Function_Reference/register_taxonomy
     *
     * @param array $params Taxonomy arguments to override defaults.
     *
     * @return \Themosis\Taxonomy\TaxonomyBuilder
     */
    public function set(array $params = [])
    {
        // Override custom taxonomy arguments if given.
        $this->datas['args'] = array_replace_recursive($this->datas['args'], $params);

        // Trigger the 'init' event in order to register the custom taxonomy.
        // Check if we are not already called by a method attached to the `init` hook.
        $current = current_filter();

        if ('init' === $current) {
            // If inside an `init` action, simply call the register method.
            $this->register();
        } else {
            // Out of an `init` action, call the hook.
            $this->action->add('init', [$this, 'register']);
        }

        // Register each custom taxonomy instance into the container.
        $this->container->instance($this->prefix.'.'.$this->datas['name'], $this);

        return $this;
    }

    /**
     * Triggered by the 'init' action/event.
     * Register the custom taxonomy.
     */
    public function register()
    {
        register_taxonomy($this->datas['name'], $this->datas['postType'], $this->datas['args']);
    }

    /**
     * Link the taxonomy to its custom post type. Allow the taxonomy
     * to be found in 'parse_query' or 'pre_get_posts' filters.
     *
     * @link http://codex.wordpress.org/Function_Reference/register_taxonomy_for_object_type
     *
     * @return \Themosis\Taxonomy\TaxonomyBuilder
     */
    public function bind()
    {
        foreach ($this->datas['postType'] as $objectType) {
            register_taxonomy_for_object_type($this->datas['name'], $objectType);
        }

        return $this;
    }

    /**
     * Set the taxonomy default arguments.
     *
     * @param array  $posttypes The post type names to attach the taxonomy to.
     * @param string $plural    The plural display name.
     * @param string $singular  The singular display name.
     *
     * @return array
     */
    protected function setDefaultArguments($posttypes, $plural, $singular)
    {
        $labels = [
            'name' => $plural,
            'singular_name' => $singular,
            'menu_name' => $plural
        ];

        $defaults = [
            'label' => $plural,
            'labels' => $labels,
            'public' => true
        ];

        /*
         * Check if defined custom post type has custom statuses.
         * If it has custom statuses, change the default update count callback function
         * before registering.
         */
        foreach ($posttypes as $posttype) {
            if (isset($this->container['posttype.'.$posttype])) {
                $posttypeInstance = $this->container['posttype.'.$posttype];

                if ($posttypeInstance->has_status()) {
                    // Tell WordPress to count posts that are associated to a term.
                    $defaults['update_count_callback'] = '_update_generic_term_count';
                    break; // No need to loop further if one of the post has custom status.
                }
            }
        }

        return $defaults;
    }

    /**
     * Return a defined taxonomy property.
     *
     * @param null $property
     *
     * @return array
     * 
     * @throws TaxonomyException
     */
    public function get($property = null)
    {
        $args = [
            'slug' => $this->datas['name'],
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
     * Register/display taxonomy custom fields.
     * Can be called without the need to create a custom taxonomy previously (pass taxonomy name as second
     * parameter to the method).
     *
     * @param array  $fields   A list of custom fields to use.
     * @param string $taxonomy The taxonomy name used to attach the fields to.
     *
     * @return \Themosis\Taxonomy\TaxonomyBuilder
     */
    public function addFields(array $fields, $taxonomy = '')
    {
        // Check taxonomy.
        if (empty($taxonomy) && isset($this->datas['name'])) {
            $taxonomy = $this->datas['name'];
        }

        // Second check if $taxonomy has been omitted.
        if (empty($taxonomy)) {
            return $this;
        }

        // Save fields with the instance.
        $this->fields = $fields;

        /*
         * Let's initialize term meta...
         */
        $current = current_filter();

        if ('init' === $current) {
            // If inside an `init` action, simply call the method.
            $this->registerFields();
        } else {
            // Out of an `init` action, call the hook.
            $this->action->add('init', [$this, 'registerFields']);
        }

        /*
         * Let's add the fields...
         */
        $this->action->add($taxonomy.'_add_form_fields', [$this, 'displayAddFields']);
        $this->action->add($taxonomy.'_edit_form_fields', [$this, 'displayEditFields']);

        /*
         * Let's handle the save...
         */
        $this->action->add('create_'.$taxonomy, [$this, 'save']);
        $this->action->add('edit_'.$taxonomy, [$this, 'save']);

        return $this;
    }

    /**
     * Register the term meta.
     */
    public function registerFields()
    {
        foreach ($this->fields as $field) {
            register_meta('term', $field['name'], [$this, 'sanitizeField']);
        }
    }

    /**
     * Used to run the sanitize callbacks.
     *
     * @param mixed  $value
     * @param string $key
     * @param string $type
     *
     * @return mixed
     */
    public function sanitizeField($value, $key, $type)
    {
        $rules = $this->datas['rules.sanitize'];

        $rule = isset($rules[$key]) ? $rules[$key] : ['html'];

        $vals = [];

        // Check sanitization for infinite fields.
        if (is_array($value)) {
            foreach ($value as $k => $val) {
                if (is_array($val)) {
                    foreach ($val as $subKey => $subVal) {
                        // Check if there is a sanitize method defined for inner fields.
                        if (isset($rule[$subKey]) && !is_numeric($subKey)) {
                            $vals[$k][$subKey] = $this->validator->single($subVal, $rule[$subKey]);
                        } else {
                            // If one inner field has a rule, this one is wrong for the others because $rule is an array of array.
                            if (isset($rules[$key]) && !isset($rule[$subKey])) {
                                $vals[$k][$subKey] = $this->validator->single($subVal, ['html']);
                            } else {
                                $vals[$k][$subKey] = $this->validator->single($subVal, $rule);
                            }
                        }
                    }
                }
            }
        }

        // Return parsed array of the infinite field.
        if (!empty($vals)) {
            return $vals;
        }

        return $this->validator->single($value, $rule);
    }

    /**
     * Display fields on add form screen.
     */
    public function displayAddFields()
    {
        $this->setNonce();
        echo $this->view->make('_themosisCoreTaxonomyAdd', ['fields' => $this->fields])->render();
    }

    /**
     * Display fields on edit form screen.
     *
     * @param \stdClass $term The term object
     */
    public function displayEditFields($term)
    {
        $this->setNonce();

        foreach ($this->fields as $field) {
            $value = get_term_meta($term->term_id, $field['name'], true);
            $field['value'] = $this->getValue($term->term_id, $field, $value);
            echo $this->view->make('_themosisCoreTaxonomyEdit', ['field' => $field])->render();
        }
    }

    /**
     * Set a custom nonce.
     */
    protected function setNonce()
    {
        wp_nonce_field('taxonomy_set_fields', '_themosisnonce');
    }

    /**
     * Return a default value for the custom fields.
     * 
     * @param int                           $term_id
     * @param \Themosis\Field\Fields\IField $field
     * @param string                        $value
     *
     * @return mixed|string
     */
    protected function getValue($term_id, $field, $value = '')
    {
        if (isset($_POST[$field['name']])) {
            // Check if a "save" method exists. The method will parse the $_POST value
            // and transform it for DB save. Ex.: transform an array to string or int...
            if (method_exists($field, 'save')) {
                // The field save method
                $value = $field->save($_POST[$field['name']], $term_id);
            } else {
                // No "save" method, only fetch the $_POST value.
                $value = $_POST[$field['name']];
            }
        } else {
            // If nothing...get a default value...
            $value = $this->parseValue($field, $value);
        }

        return $value;
    }

    /**
     * Save term custom field data to database.
     * 
     * @param int $term_id
     */
    public function save($term_id)
    {
        if (!isset($_POST['_themosisnonce']) || !wp_verify_nonce($_POST['_themosisnonce'], 'taxonomy_set_fields')) {
            return;
        }

        foreach ($this->fields as $field) {
            $value = $this->getValue($term_id, $field);
            update_term_meta($term_id, $field['name'], $value);
        }
    }

    /**
     * Sanitize custom fields values by using passed rules.
     *
     * @param array $rules Sanitize rules.
     *
     * @return \Themosis\Taxonomy\TaxonomyBuilder
     */
    public function sanitize(array $rules = [])
    {
        $this->datas['rules.sanitize'] = $rules;

        return $this;
    }
}
