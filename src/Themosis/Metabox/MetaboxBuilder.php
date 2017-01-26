<?php

namespace Themosis\Metabox;

use Illuminate\View\View;
use Themosis\Foundation\DataContainer;
use Themosis\Field\Wrapper;
use Themosis\Hook\IHook;
use Themosis\User\User;
use Themosis\Validation\ValidationBuilder;

class MetaboxBuilder extends Wrapper implements IMetabox
{
    /**
     * Metabox instance datas.
     *
     * @var \Themosis\Foundation\DataContainer
     */
    protected $datas;

    /**
     * The metabox view.
     *
     * @var \Illuminate\View\View
     */
    protected $view;

    /**
     * The metabox view sections.
     *
     * @var array
     */
    protected $sections = [];

    /**
     * A validator instance.
     */
    protected $validator;

    /**
     * @var IHook
     */
    protected $action;

    /**
     * @var IHook
     */
    protected $filter;

    /**
     * Mapping rules.
     *
     * @var array
     */
    protected $mappings = [];

    /**
     * The current user instance.
     *
     * @var \Themosis\User\User
     */
    protected $user;

    /**
     * Whether or not check for user capability.
     *
     * @var bool
     */
    protected $check = false;

    /**
     * The capability to check.
     *
     * @var string
     */
    protected $capability;

    /**
     * The nonce name.
     *
     * @var string
     */
    protected $nonce = '_themosisnonce';

    /**
     * The nonce action.
     *
     * @var string
     */
    protected $nonceAction = 'metabox';

    /**
     * Build a metabox instance.
     *
     * @param DataContainer                          $datas     The metabox properties.
     * @param \Illuminate\View\View                  $view      The metabox default view.
     * @param \Themosis\Validation\ValidationBuilder $validator
     * @param \Themosis\User\User                    $user
     * @param IHook                                  $action
     */
    public function __construct(DataContainer $datas, View $view, ValidationBuilder $validator, User $user, IHook $action, IHook $filter)
    {
        $this->datas = $datas;
        $this->view = $view;
        $this->validator = $validator;
        $this->user = $user;
        $this->action = $action;
        $this->filter = $filter;

        // Handle save action for fields.
        $action->add('save_post', [$this, 'save']);
    }

    /**
     * Set a new metabox.
     *
     * @param string                $title    The metabox title.
     * @param string                $postType The metabox parent slug name.
     * @param array                 $options  Metabox extra options.
     * @param \Illuminate\View\View $view     The metabox view.
     *
     * @return object
     */
    public function make($title, $postType, array $options = [], View $view = null)
    {
        $this->datas['title'] = $title;
        $this->datas['postType'] = $postType;
        $this->datas['options'] = $this->parseOptions($options);

        if (!is_null($view)) {
            $this->view = $view;
        }

        return $this;
    }

    /**
     * Build the set metabox.
     *
     * @param array $fields A list of fields to display.
     *
     * @return \Themosis\Metabox\MetaboxBuilder
     */
    public function set(array $fields = [])
    {
        // Check if sections are defined.
        $this->sections = $this->getSections($fields);

        $this->datas['fields'] = $fields;

        $this->action->add('add_meta_boxes', [$this, 'display']);

        return $this;
    }

    /**
     * Restrict access to a specific user capability.
     *
     * @param string $capability
     *
     * @return \Themosis\Metabox\MetaboxBuilder
     */
    public function can($capability)
    {
        $this->capability = $capability;
        $this->check = true;

        return $this;
    }

    /**
     * Map a meta key to a current post data. Be careful as you might override
     * important post data. Use it with precaution.
     *
     * @param array $mappings A list of key/value pairs defining the mappings. Key is the meta_key and its value is the post data name/key.
     */
    public function map(array $mappings)
    {
        $this->mappings = $this->parseMappings($mappings);

        $this->filter->add('wp_insert_post_data', [$this, 'map_metadata'], 10, 2);
    }

    protected function parseMappings(array $mappings)
    {
        $maps = [];
        $allowed = [
            'post_author' => ['is_int', 'strlen' => 20],
            'post_date' => 'is_string',
            'post_date_gmt' => 'is_string',
            'post_content' => 'is_string',
            'post_content_filtered' => 'is_string',
            'post_title' => 'is_string',
            'post_excerpt' => 'is_string',
            'post_status' => ['is_string', 'strlen' => 20],
            'post_type' => ['is_string', 'strlen' => 20],
            'comment_status' => ['is_string', 'strlen' => 20],
            'ping_status' => ['is_string', 'strlen' => 20],
            'post_password' => ['is_string', 'strlen' => 20],
            'post_name' => ['is_string', 'strlen' => 200],
            'to_ping' => 'is_string',
            'pinged' => 'is_string',
            'post_modified' => 'is_string',
            'post_modified_gmt' => 'is_string',
            'post_parent' => ['is_int', 'strlen' => 20],
            'menu_order' => ['is_int', 'strlen' => 11],
            'post_mime_type' => ['is_string', 'strlen' => 100],
            'guid' => ['is_string', 'strlen' => 255],
        ];

        foreach ($mappings as $meta_key => $post_key) {
            if (in_array($post_key, array_keys($allowed))) {
                $maps[$meta_key] = $post_key;
            }
        }

        return $maps;
    }

    /**
     * Handle the mapping.
     *
     * @param array $data The post data.
     * @param array $raw  Sanitized but unmodified post data.
     *
     * @return array
     */
    public function map_metadata($data, $raw)
    {
        $post_id = isset($_POST['post_ID']) ? esc_attr($_POST['post_ID']) : false;

        if (!$post_id) {
            return $data;
        }

        foreach ($this->mappings as $meta_key => $post_key) {
            $field = array_filter($this->getFields(), function ($field) use ($meta_key) {
                return $meta_key == $field['name'];
            });

            $field = array_shift($field);

            if (isset($_POST[$field['name']])) {
                // Check if a "save" method exists. The method will parse the $_POST value
                // and transform it for DB save. Ex.: transform an array to string or int...
                if (method_exists($field, 'save')) {
                    // The field save method
                    $value = $field->save($_POST[$field['name']], $post_id);
                } else {
                    // No "save" method, only fetch the $_POST value.
                    $value = $_POST[$field['name']];
                }
            } else {
                // If nothing...setup a default value...
                $value = $this->parseValue($field);
            }

            // Apply validation if defined.
            // Check if the rule exists for the field in order to validate.
            if (isset($this->datas['rules'][$field['name']])) {
                $rules = $this->datas['rules'][$field['name']];

                // Check if $rules array is an associative array
                if ($this->validator->isAssociative($rules) && 'infinite' == $field->getFieldType()) {
                    // Check Infinite fields validation.
                    foreach ($value as $row => $rowValues) {
                        foreach ($rowValues as $name => $val) {
                            if (isset($rules[$name])) {
                                $value[$row][$name] = $this->validator->single($val, $rules[$name]);
                            }
                        }
                    }
                } else {
                    $value = $this->validator->single($value, $this->datas['rules'][$field['name']]);
                }
            }

            // Assign value to post data.
            if (isset($data[$post_key])) {
                $data[$post_key] = $value;
            }
        }

        return $data;
    }

    /**
     * Check if a template is defined within the metabox.
     *
     * @return bool
     */
    protected function hasTemplate()
    {
        if (isset($this->datas['options']['template']) && !empty($this->datas['options']['template'])) {
            return true;
        }

        return false;
    }

    /**
     * The wrapper display method.
     *
     * @param string $postType The postType name.
     */
    public function display($postType)
    {
        if ($this->check && !$this->user->can($this->capability)) {
            return;
        }

        // Look if a template is defined.
        // Display the metabox on pages/posts that have a template registered.
        if ($this->hasTemplate() && $postType == $this->datas['postType']) {
            // Fetch current ID (for cpts only).
            $postID = themosis_get_post_id();
            $template = get_post_meta($postID, '_wp_page_template', true);

            // Check if a template is attached to the post/page.
            if ($template == $this->datas['options']['template']) {
                // Add a metabox for post/pages with a registered template.
                $this->addMetabox();
            }
        } else {
            // Add a metabox for no templates cases.
            $this->addMetabox();
        }
    }

    /**
     * Call the core function add_meta_box in order to output
     * a metabox.
     */
    protected function addMetabox()
    {
        // Fields are passed to the metabox $args parameter.
        add_meta_box($this->datas['options']['id'], $this->datas['title'], [$this, 'build'], $this->datas['postType'], $this->datas['options']['context'], $this->datas['options']['priority'], $this->datas['fields']);
    }

    /**
     * Call by "add_meta_box", build the HTML code.
     *
     * @param \WP_Post $post  The WP_Post object.
     * @param array    $datas The metabox $args and associated fields.
     *
     * @throws MetaboxException
     */
    public function build($post, array $datas)
    {
        // Add nonce fields
        wp_nonce_field($this->nonceAction, $this->nonce);

        // Set the default 'value' attribute regarding sections.
        if (!empty($this->sections)) {
            foreach ($this->sections as $section) {
                if (isset($datas['args'][$section])) {
                    $fields = $datas['args'][$section];

                    // Set the default 'value' property of all fields.
                    $this->setDefaultValue($post, $fields);
                }
            }
        } else {
            // Set the default 'value' property of all fields.
            $this->setDefaultValue($post, $datas['args']);
        }

        $this->render($datas['args'], $post);
    }

    /**
     * The wrapper install method. Save container values.
     *
     * @param int $postId The post ID value.
     */
    public function save($postId)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        $nonceName = (isset($_POST[$this->nonce])) ? $_POST[$this->nonce] : $this->nonce;
        if (!wp_verify_nonce($nonceName, $this->nonceAction)) {
            return;
        }

        // Grab current custom post type name.
        $postType = get_post_type($postId);

        // Check user capability.
        if ($this->check && $this->datas['postType'] === $postType) {
            if (!$this->user->can($this->capability)) {
                return;
            }
        }

        // Check current post type...avoid to register fields for all registered post type.
        if ($postType !== $this->datas['postType']) {
            return;
        }

        $fields = $this->getFields();

        // Register post meta.
        $this->register($postId, $fields);
    }

    /**
     * Retrieve the list of defined fields.
     *
     * @return array|mixed
     */
    public function getFields()
    {
        $fields = [];

        // Loop through the registered fields.
        // With sections.
        if (!empty($this->sections)) {
            foreach ($this->datas['fields'] as $section => $fs) {
                /*
                 * Loop through section inner fields
                 * and add them to the default list.
                 */
                foreach ($fs as $f) {
                    $fields[] = $f;
                }
            }
        } else {
            $fields = $this->datas['fields'];
        }

        return $fields;
    }

    /**
     * Register validation rules for the custom fields.
     *
     * @param array $rules A list of field names and their associated validation rule.
     *
     * @return \Themosis\Metabox\MetaboxBuilder
     */
    public function validate(array $rules = [])
    {
        $this->datas['rules'] = $rules;

        return $this;
    }

    /**
     * Register the metabox and its fields into the DB.
     *
     * @param int   $postId
     * @param array $fields
     */
    protected function register($postId, array $fields)
    {
        foreach ($fields as $field) {
            // Default value... (init var)
            $value = '';

            if (isset($_POST[$field['name']])) {
                // Check if a "save" method exists. The method will parse the $_POST value
                // and transform it for DB save. Ex.: transform an array to string or int...
                if (method_exists($field, 'save')) {
                    // The field save method
                    $value = $field->save($_POST[$field['name']], $postId);
                } else {
                    // No "save" method, only fetch the $_POST value.
                    $value = $_POST[$field['name']];
                }
            } else {
                // If nothing...setup a default value...
                $value = $this->parseValue($field);
            }

            // Apply validation if defined.
            // Check if the rule exists for the field in order to validate.
            if (isset($this->datas['rules'][$field['name']])) {
                $rules = $this->datas['rules'][$field['name']];

                // Check if $rules array is an associative array
                if ($this->validator->isAssociative($rules) && 'infinite' == $field->getFieldType()) {
                    // Check Infinite fields validation.
                    foreach ($value as $row => $rowValues) {
                        foreach ($rowValues as $name => $val) {
                            if (isset($rules[$name])) {
                                $value[$row][$name] = $this->validator->single($val, $rules[$name]);
                            }
                        }
                    }
                } else {
                    $value = $this->validator->single($value, $this->datas['rules'][$field['name']]);
                }
            }

            // Multiple meta keys: same name for meta_key but with different values.
            // Infinite fields cannot be used in meta query...
            if (is_array($value) && ('infinite' !== $field->getFieldType() && 'collection' !== $field->getFieldType())) {
                // Retrieve existing "old_values".
                $old_values = get_post_meta($postId, $field['name'], false); // array
                foreach ($value as $val) {
                    if (in_array($val, $old_values)) {
                        $old_value = array_filter($old_values, function ($old) use ($val) {
                            return $old === $val;
                        });

                        update_post_meta($postId, $field['name'], $val, $old_value);
                    } elseif (!empty($val)) {
                        add_post_meta($postId, $field['name'], $val, false);
                    }
                    // Check for removed data...
                    $notupdated_values = array_diff($old_values, $value);

                    if (!empty($notupdated_values)) {
                        foreach ($notupdated_values as $value_to_delete) {
                            delete_post_meta($postId, $field['name'], $value_to_delete);
                        }
                    }
                }

                /*
                 * If no new values are passed but have existed,
                 * then remove everything.
                 */
                if (empty($value) && !empty($old_values)) {
                    delete_post_meta($postId, $field['name']);
                }
            } else {
                // Single meta key
                $old_value = get_post_meta($postId, $field['name'], true); // unique value

                if (!empty($old_value)) {
                    update_post_meta($postId, $field['name'], $value, $old_value);
                } elseif (!empty($value)) {
                    update_post_meta($postId, $field['name'], $value);
                } else {
                    delete_post_meta($postId, $field['name']);
                }
            }
        }
    }

    /**
     * Check metabox options: context, priority.
     *
     * @param array $options The metabox options.
     *
     * @return array
     */
    protected function parseOptions(array $options)
    {
        return wp_parse_args($options, [
            'context' => 'normal',
            'priority' => 'default',
            'id' => md5($this->datas['title']),
            'template' => '',
        ]);
    }

    /**
     * Set the metabox view sections.
     *
     * @param array $fields
     *
     * @return array
     */
    protected function getSections(array $fields)
    {
        $sections = [];

        foreach ($fields as $section => $subFields) {
            if (!is_numeric($section)) {
                array_push($sections, $section);
            }
        }

        return $sections;
    }

    /**
     * Set the default 'value' property for all fields.
     *
     * @param \WP_Post $post
     * @param array    $fields
     */
    protected function setDefaultValue(\WP_Post $post, array $fields)
    {
        foreach ($fields as $field) {
            // Check if saved value
            switch ($field->getFieldType()) {
                case 'checkbox':
                case 'radio':
                case 'select':
                    $value = get_post_meta($post->ID, $field['name'], false);
                    break;

                default:
                    $value = get_post_meta($post->ID, $field['name'], true);
            }

            // If none of the above condition is matched
            // simply assign the post meta default or saved value.
            $field['value'] = $this->parseValue($field, $value);
        }
    }

    /**
     * Render the metabox.
     *
     * @param array    $fields
     * @param \WP_Post $post
     */
    protected function render(array $fields, $post)
    {
        $this->view->with([
            '__fields' => $fields, // Pass the custom fields
            '__metabox' => $this, // Pass the metabox instance
            '__post' => $post, // Pass the WP_Post instance
        ]);

        echo $this->view->render();
    }

    /**
     * Allow a user to pass custom datas to
     * the metabox main view.
     *
     * @param string|array $key
     * @param mixed        $value
     *
     * @return \Themosis\Metabox\MetaboxBuilder
     */
    public function with($key, $value = null)
    {
        $this->view->with($key, $value);

        return $this;
    }
}
