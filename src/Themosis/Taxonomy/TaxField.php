<?php

namespace Themosis\Taxonomy;

/**
 * TaxField class.
 * 
 * Allow the user to add custom fields to a taxonomy.
 */
class TaxField
{
    /**
     * The taxonomy slug.
     *
     * @var string
     */
    private $slug;

    /**
     * Tell if the taxonomy exists.
     *
     * @var bool
     */
    private $exists = false;

    /**
     * The custom fields of the taxonomy.
     *
     * @var array
     */
    private $fields = array();

    /**
     * The TaxField constructor.
     *
     * @param string $taxonomySlug The taxonomy slug used by action hooks.
     */
    private function __construct($taxonomySlug)
    {
        $this->slug = $taxonomySlug;

        /*-----------------------------------------------------------------------*/
        // Check if the taxonomy exists before going further.
        /*-----------------------------------------------------------------------*/
        add_action('wp_loaded', array($this, 'check'));
    }

    /**
     * Init the custom taxonomy field.
     *
     * @param string $taxonomySlug The taxonomy slug.
     *
     * @throws TaxonomyException
     *
     * @return \Themosis\Taxonomy\TaxField
     */
    public static function make($taxonomySlug)
    {
        $slug = trim($taxonomySlug);

        if (!empty($slug) && is_string($slug)) {
            return new static($slug);
        } else {
            throw new TaxonomyException('String expected as a parameter.');
        }
    }

    /**
     * Check if the taxonomy exists. Call by the action hook 'wp_loaded'.
     * If not, throw an exception.
     *
     * @throws TaxonomyException
     * @ignore
     */
    public function check()
    {
        if (taxonomy_exists($this->slug)) {

            /*-----------------------------------------------------------------------*/
            // Set the exists property to true.
            // Allow the user to define the fields later with the "set" method.
            /*-----------------------------------------------------------------------*/
            $this->exists = true;
        } else {
            throw new TaxonomyException('The taxonomy slug "'.$this->slug.'" does not exists.');
        }
    }

    /**
     * Set the custom fields for the taxonomy.
     *
     * @param array $fields A list of fields.
     *
     * @throws TaxonomyException
     *
     * @return \Themosis\Taxonomy\TaxField
     */
    public function set(array $fields)
    {
        if (is_array($fields) && !empty($fields)) {

            /*-----------------------------------------------------------------------*/
            // Parse the fields and save them to the instance property.
            /*-----------------------------------------------------------------------*/
            $this->fields = $this->parse($fields, $this->slug);

            /*-----------------------------------------------------------------------*/
            // Add the field to the "add term page"
            // {$taxonomy_slug}_add_form_fields
            /*-----------------------------------------------------------------------*/
            $slug = $this->slug.'_add_form_fields';

            add_action($slug, array($this, 'addFields'));

            /*-----------------------------------------------------------------------*/
            // Add the field to the "edit term page"
            /*-----------------------------------------------------------------------*/
            $slug = $this->slug.'_edit_form_fields';

            add_action($slug, array($this, 'editFields'));

            /*-----------------------------------------------------------------------*/
            // Register the save hooks on the add + edit pages.
            /*-----------------------------------------------------------------------*/
            add_action('edited_'.$this->slug, array($this, 'save'), 10, 2);
            add_action('create_'.$this->slug, array($this, 'save'), 10, 2);

            /*-----------------------------------------------------------------------*/
            // Register the delete hook in order to remove the custom fields
            // from the options table.
            /*-----------------------------------------------------------------------*/
            add_action('delete_term', array($this, 'delete'));

            return $this;
        } else {
            throw new TaxonomyException('Array expected as a parameter.');
        }
    }

    /**
     * Display the custom fields on the add terms page.
     *
     * @ignore
     */
    public function addFields()
    {
        /*-----------------------------------------------------------------------*/
        // Output the custom fields
        /*-----------------------------------------------------------------------*/
        TaxFieldRenderer::render('add', $this->fields);
    }

    /**
     * Display the custom fields on the edit term page.
     *
     * @param \stdClass $term The term object passed by WordPress.
     * @ignore
     */
    public function editFields(\stdClass $term)
    {
        /*-----------------------------------------------------------------------*/
        // Output the custom fields
        /*-----------------------------------------------------------------------*/
        TaxFieldRenderer::render('edit', $this->fields, $term);
    }

    /**
     * Save the fields values in the options table.
     *
     * @param int $term_id The term ID.
     * @ignore
     */
    public function save($term_id)
    {
        if (isset($_POST[$this->slug])) {

            /*-----------------------------------------------------------------------*/
            // Option unique key
            /*-----------------------------------------------------------------------*/
            $optionKey = $this->slug.'_'.$term_id;

            /*-----------------------------------------------------------------------*/
            // Retrieve an existing value if it exists...
            /*-----------------------------------------------------------------------*/
            $term_meta = get_option($optionKey);

            /*-----------------------------------------------------------------------*/
            // Get all fields names's key
            /*-----------------------------------------------------------------------*/
            $cat_keys = array_keys($_POST[$this->slug]);

            foreach ($cat_keys as $key) {
                if (isset($_POST[$this->slug][$key])) {
                    $term_meta[$key] = $_POST[$this->slug][$key];
                }
            }

            /*-----------------------------------------------------------------------*/
            // Save the fields
            /*-----------------------------------------------------------------------*/
            update_option($optionKey, $term_meta);
        }
    }

    /**
     * Delete the fields from the database.
     *
     * @param \stdClass $term    The term object.
     * @param int       $term_id The term ID.
     * @ignore
     */
    public function delete($term_id)
    {
        $key = $this->slug.'_'.$term_id;

        delete_option($key);
    }

    /**
     * Parse the fields and mix them with a default one.
     *
     * @param array  $fields       The defined fields and their properties.
     * @param string $taxonomySlug The taxonomy slug.
     *
     * @return array The parsed array of fields with a TaxonomySlug key/value.
     */
    private function parse(array $fields, $taxonomySlug)
    {
        $newFields = array();

        foreach ($fields as $field) {
            $defaults = array(
                'name' => 'default_field',
                'title' => ucfirst($field['name']),
                'info' => '',
                'default' => '',
                'type' => 'text',
                'options' => array(),
                'class' => '',
                'multiple' => false,
                'fields' => array(),
                'taxonomy_slug' => $taxonomySlug,
            );

            /*-----------------------------------------------------------------------*/
            // Mix values from defaults and $args and then extract
            // the results as $variables
            /*-----------------------------------------------------------------------*/
            extract(wp_parse_args($field, $defaults));

            $field_args = array(
                'type' => $type,
                'name' => $name,
                'info' => $info,
                'default' => $default,
                'options' => $options,
                'label_for' => $name,
                'class' => $class,
                'title' => $title,
                'multiple' => $multiple,
                'fields' => $fields,
                'taxonomy_slug' => $taxonomy_slug,
            );

            /*-----------------------------------------------------------------------*/
            // Add new settings
            /*-----------------------------------------------------------------------*/
            $newFields[] = $field_args;
        }

        return $newFields;
    }
}
