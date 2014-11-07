<?php
namespace Themosis\Metabox;

use Themosis\Action\Action;
use Themosis\Core\DataContainer;
use Themosis\Core\Wrapper;
use Themosis\Session\Session;
use Themosis\User\User;
use Themosis\Validation\ValidationBuilder;
use Themosis\View\IRenderable;

class MetaboxBuilder extends Wrapper {

    /**
     * Metabox instance datas.
     *
     * @var \Themosis\Core\DataContainer
     */
    private $datas;

    /**
     * The metabox view.
     *
     * @var \Themosis\View\View
     */
    private $view;

    /**
     * The metabox view sections.
     *
     * @var array
     */
    private $sections = array();

    /**
     * A validator instance.
     */
    private $validator;

    /**
     * The display/install event to listen to.
     */
    private $installEvent;

    /**
     * The current user instance.
     *
     * @var \Themosis\User\User
     */
    private $user;

    /**
     * Whether or not check for user capability.
     *
     * @var bool
     */
    private $check = false;

    /**
     * The capability to check.
     *
     * @var string
     */
    private $capability;

    /**
     * Build a metabox instance.
     *
     * @param DataContainer $datas The metabox properties.
     * @param \Themosis\View\IRenderable $view The metabox default view.
     * @param \Themosis\Validation\ValidationBuilder $validator
     * @param \Themosis\User\User $user
     */
    public function __construct(DataContainer $datas, IRenderable $view, ValidationBuilder $validator, User $user)
    {
        $this->datas = $datas;
        $this->view = $view;
        $this->validator = $validator;
        $this->user = $user;
        $this->installEvent = Action::listen('add_meta_boxes', $this, 'display');
        Action::listen('save_post', $this, 'save')->dispatch();
    }

    /**
     * Set a new metabox.
     *
     * @param string $title The metabox title.
     * @param string $postType The metabox parent slug name.
     * @param array $options Metabox extra options.
     * @param \Themosis\View\IRenderable $view The metabox view.
     * @return object
     */
    public function make($title, $postType, array $options = array(), IRenderable $view = null)
    {
        $this->datas['title'] = $title;
        $this->datas['postType'] = $postType;
        $this->datas['options'] = $this->parseOptions($options);

        if (!is_null($view))
        {
            $this->view = $view;
        }

        return $this;
    }

    /**
     * Build the set metabox.
     *
     * @param array $fields A list of fields to display.
     * @return \Themosis\Metabox\MetaboxBuilder
     */
    public function set(array $fields = array())
    {
        // Check if sections are defined.
        $this->sections = $this->getSections($fields);

        $this->datas['fields'] = $fields;

        $this->installEvent->dispatch();

        return $this;
    }

    /**
     * Restrict access to a specific user capability.
     *
     * @param string $capability
     * @return void
     */
    public function can($capability)
    {
        $this->capability = $capability;
        $this->check = true;
    }

    /**
     * The wrapper display method.
     *
     * @return void
     */
    public function display()
    {
        if($this->check && !$this->user->can($this->capability)) return;

        $id = md5($this->datas['title']);

        // Fields are passed to the metabox $args parameter.
        add_meta_box($id, $this->datas['title'], array($this, 'build'), $this->datas['postType'], $this->datas['options']['context'], $this->datas['options']['priority'], $this->datas['fields']);
    }

    /**
     * Call by "add_meta_box", build the HTML code.
     *
     * @param \WP_Post $post The WP_Post object.
     * @param array $datas The metabox $args and associated fields.
     * @throws MetaboxException
     * @return void
     */
    public function build($post, array $datas)
    {
        // Add nonce fields
        wp_nonce_field(Session::nonceAction, Session::nonceName);

        // Set the default 'value' attribute regarding sections.
        if (!empty($this->sections))
        {
            foreach ($this->sections as $section)
            {
                if (isset($datas['args'][$section]))
                {
                    $fields = $datas['args'][$section];

                    // Set the default 'value' property of all fields.
                    $this->setDefaultValue($post, $fields);
                }
            }
        }
        else
        {
            // Set the default 'value' property of all fields.
            $this->setDefaultValue($post, $datas['args']);
        }

        $this->render($datas['args']);

    }

    /**
     * The wrapper install method. Save container values.
     *
     * @param int $postId The post ID value.
     * @return void
     */
    public function save($postId)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

        $nonceName = (isset($_POST[Session::nonceName])) ? $_POST[Session::nonceName] : Session::nonceName;
        if (!wp_verify_nonce($nonceName, Session::nonceAction)) return;

        // Check user capability.
        if ($this->check && $this->datas['postType'] === $_POST['post_type'])
        {
            if (!$this->user->can($this->capability)) return;
        }

        $fields = array();

        // Loop through the registered fields.
        // With sections.
        if (!empty($this->sections))
        {
            foreach ($this->datas['fields'] as $fs)
            {
                $fields = $fs;
            }
        }
        else
        {
            $fields = $this->datas['fields'];
        }

        $this->register($postId, $fields);

    }

    /**
     * Register validation rules for the custom fields.
     *
     * @param array $rules A list of field names and their associated validation rule.
     * @return \Themosis\Metabox\MetaboxBuilder
     */
    public function validate(array $rules = array())
    {
        $this->datas['rules'] = $rules;

        return $this;
    }

    /**
     * Register the metabox and its fields into the DB.
     *
     * @param int $postId
     * @param array $fields
     * @return void
     */
    private function register($postId, array $fields)
    {
        foreach($fields as $field)
        {
            $value = isset($_POST[$field['name']]) ? $_POST[$field['name']] : $this->parseValue($field);

            // Apply validation if defined.
            // Check if the rule exists for the field in order to validate.
            if (isset($this->datas['rules'][$field['name']]))
            {
                $rules = $this->datas['rules'][$field['name']];

                // Check if $rules array is an associative array
                if ($this->validator->isAssociative($rules) && 'infinite' == $field->getFieldType())
                {
                    // Check Infinite fields validation.
                    foreach ($value as $row => $rowValues)
                    {
                        foreach ($rowValues as $name => $val)
                        {
                            if (isset($rules[$name]))
                            {
                                $value[$row][$name] = $this->validator->single($val, $rules[$name]);
                            }
                        }
                    }

                }
                else
                {
                    $value = $this->validator->single($value, $this->datas['rules'][$field['name']]);
                }
            }

            update_post_meta($postId, $field['name'], $value);

        }
    }

    /**
     * Check metabox options: context, priority.
     *
     * @param array $options The metabox options.
     * @return array
     */
    private function parseOptions(array $options)
    {
        // Default
        if (empty($options))
        {
            return array(
                'context'   => 'normal',
                'priority'  => 'default'
            );
        }

        // If options defined...
        $newOptions = array();

        $allowed = array('context', 'priority');

        foreach ($options as $param => $value)
        {
            if (in_array($param, $allowed))
            {
                $newOptions[$param] = $value;
            }
        }

        return $newOptions;

    }

    /**
     * Set the metabox view sections.
     *
     * @param array $fields
     * @return array
     */
    private function getSections(array $fields)
    {
        $sections = array();

        foreach ($fields as $section => $subFields)
        {
            if (!is_numeric($section))
            {
                array_push($sections, $section);
            }
        }

        return $sections;
    }

    /**
     * Set the default 'value' property for all fields.
     *
     * @param \WP_Post $post
     * @param array $fields
     * @return void
     */
    private function setDefaultValue(\WP_Post $post, array $fields)
    {
        foreach ($fields as $field)
        {
            // Check if saved value
            $value = get_post_meta($post->ID, $field['name'], true);

            // If none of the above condition is matched
            // simply assign the post meta default or saved value.
            $field['value'] = $this->parseValue($field, $value);
        }
    }

    /**
     * Render the metabox.
     *
     * @param array $fields
     * @return void
     */
    private function render(array $fields)
    {
        // Pass the fields to the main metabox view.
        $this->view->with('__fields', $fields);

        // Pass the MetaboxBuilder instance to the main view.
        $this->view->with('__metabox', $this);

        echo($this->view->render());
    }

    /**
     * Allow a user to pass custom datas to
     * the metabox main view.
     *
     * @param string|array $key
     * @param mixed $value
     * @return \Themosis\Metabox\MetaboxBuilder
     */
    public function with($key, $value = null)
    {
        $this->view->with($key, $value);

        return $this;
    }

}