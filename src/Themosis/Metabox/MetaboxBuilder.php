<?php
namespace Themosis\Metabox;

use Themosis\Action\Action;
use Themosis\Core\DataContainer;
use Themosis\Core\Wrapper;
use Themosis\Core\WrapperView;
use Themosis\Session\Session;
use Themosis\Validation\ValidationBuilder;

class MetaboxBuilder extends Wrapper {

    /**
     * Metabox instance datas.
     *
     * @var array
     */
    private $datas;

    /**
     * The metabox view.
     *
     * @var
     */
    private $view;

    /**
     * A validator instance.
     */
    private $validator;

    /**
     * The display/install event to listen to.
     */
    private $installEvent;

    /**
     * Build a metabox instance.
     *
     * @param DataContainer $datas The metabox properties.
     * @param \Themosis\Core\WrapperView $view The metabox default view.
     * @param \Themosis\Validation\ValidationBuilder $validator
     */
    public function __construct(DataContainer $datas, WrapperView $view, ValidationBuilder $validator)
    {
        $this->datas = $datas;
        $this->view = $view;
        $this->validator = $validator;
        $this->installEvent = Action::listen('add_meta_boxes', $this, 'display');
        Action::listen('save_post', $this, 'save')->dispatch();
    }

    /**
     * Set a new metabox.
     *
     * @param string $title The metabox title.
     * @param string $postType The metabox parent slug name.
     * @param array $options Metabox extra options.
     * @param \Themosis\Core\WrapperView $view The metabox view.
     * @return object
     */
    public function make($title, $postType, array $options = array(), WrapperView $view = null)
    {
        $this->datas['title'] = $title;
        $this->datas['postType'] = $postType;
        $this->datas['options'] = $this->parseOptions($options);

        if(!is_null($view)){
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
        $this->datas['fields'] = $fields;
        $this->installEvent->dispatch();

        return $this;
    }

    /**
     * The wrapper display method.
     *
     * @return void
     */
    public function display()
    {
        $id = md5($this->datas['title']);

        // Fields are passed to the metabox $args parameter.
        add_meta_box($id, $this->datas['title'], array($this, 'build'), $this->datas['postType'], $this->datas['options']['context'], $this->datas['options']['priority'], $this->datas['fields']);
    }

    /**
     * Call by "add_meta_box", build the HTML code.
     *
     * @param \WP_Post $post The WP_Post object.
     * @param array $datas The metabox $args and associated fields.
     * @return void
     */
    public function build($post, array $datas)
    {
        // Add nonce fields
        wp_nonce_field(Session::nonceAction, Session::nonceName);

        // Build all the html with the fields
        // Place the fields at the right section
        foreach($this->view->getSections() as $section){

            if(isset($datas['args'][$section])){

                foreach($datas['args'][$section] as $field){

                    // Set the value property of the $field
                    $field['value'] = get_post_meta($post->ID, $field['name'], true);

                    // Add the rendered field view.
                    $this->view->fillSection($section, $field->metabox());

                }

            }

        }

        // Render the full content.
        $this->view->render();
    }

    /**
     * The wrapper install method. Save container values.
     *
     * @param int $postId The post ID value.
     * @return void
     */
    public function save($postId)
    {
        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

        $nonceName = (isset($_POST[Session::nonceName])) ? $_POST[Session::nonceName] : Session::nonceName;
        if (!wp_verify_nonce($nonceName, Session::nonceAction)) return;

        // Loop through the registered fields.
        foreach($this->datas['fields'] as $fields){

            foreach($fields as $field){

                $value = $this->parseValue($_POST, $field);

                // Apply validation if defined.
                // Check if the rule exists for the field in order to validate.
                if(isset($this->datas['rules'][$field['name']])){

                    $rules = $this->datas['rules'][$field['name']];
                    // Check if $rules array is an associative array
                    if($this->validator->isAssociative($rules) && 'infinite' == $field->getFieldType()){
                        // Check Infinite fields validation.
                        foreach($value as $row => $rowValues){
                            foreach($rowValues as $name => $val){
                                if(isset($rules[$name])){
                                    $value[$row][$name] = $this->validator->single($val, $rules[$name]);
                                }
                            }
                        }

                    } else {
                        $value = $this->validator->single($value, $this->datas['rules'][$field['name']]);
                    }
                }

                update_post_meta($postId, $field['name'], $value);

            }

        }

    }

    /**
     * Register validation rules for the custom fields.
     *
     * @param array $rules A list of field names and their associated validation rule.
     * @return void
     */
    public function validate(array $rules = array())
    {
        $this->datas['rules'] = $rules;
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
        if(empty($options)){

            return array(
                'context'   => 'normal',
                'priority'  => 'default'
            );

        }

        // If options defined...
        $newOptions = array();

        $allowed = array('context', 'priority');

        foreach ($options as $param => $value) {

            if (in_array($param, $allowed)) {

                $newOptions[$param] = $value;

            }

        }

        return $newOptions;

    }

}