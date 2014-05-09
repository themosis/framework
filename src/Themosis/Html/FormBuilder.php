<?php
namespace Themosis\Html;

use Themosis\Configuration\Application;
use Themosis\Route\Request;
use Themosis\Session\Session;

class FormBuilder {

    /**
     * An HtmlBuilder instance.
     * @var HtmlBuilder
     */
    private $html;

    /**
     * Define a FormBuilder instance.
     *
     * @param HtmlBuilder $html
     */
    public function __construct(HtmlBuilder $html)
    {
        $this->html = $html;
    }

    /**
     * Build opening tags for a form
     *
     * @param string $action The action attribute value.
     * @param string $method The request method. Default to 'POST'.
     * @param bool $ssl Default value is false. True converts to https URL.
     * @param array $attributes An array of form attributes.
     * @return string The <form> open tag.
     */
    public function open($action = null, $method = 'POST', $ssl = false, array $attributes = array())
    {
        $attributes['action'] = $this->action($action, $ssl);
        $attributes['method'] = $this->method($method);

        // If a character encoding has not been specified in the attributes, we will
        // use the default encoding as specified in the application configuration
        // file for the "accept-charset" attribute.
        if (!array_key_exists('accept-charset', $attributes)) {
            $attributes['accept-charset'] = Application::get('encoding');
        }

        // ADD NONCE FIELDS
        // IF 'POST' METHOD
        // HELP TO AVOID CSRF
        $append = '';

        if ($attributes['method'] === 'POST') {
            $append = wp_nonce_field(Session::nonceAction, Session::nonceName, true, false);
        }

        return '<form'.$this->html->attributes($attributes).'>'.$append;
    }

    /**
     * Build the closing tag
     *
     * @return string The </form> close tag.
     */
    public function close()
    {
        return '</form>';
    }

    /**
     * Define the action attribute
     *
     * @param string $action The action attribute value.
     * @param bool $ssl Tell to set the URL to https or not.
     * @return string The converted action attribute value.
     */
    private function action($action, $ssl)
    {
        $action = trim($action);
        $ssl = (bool) $ssl;

        // Check the given path
        // If none given, set to the current page url
        $uri = ($action === null || empty($action)) ? Request::foundation()->getPathInfo() : '/'.trim($action, '/').'/';

        // Build the action url
        // Check if we are using ssl or not and build the url.
        $action = (is_ssl() || $ssl) ? 'https://'.Request::foundation()->getHttpHost().$uri : 'http://'.Request::foundation()->getHttpHost().$uri;

        return $action;
    }

    /**
     * Define the form method attribute
     *
     * @param string $method The request method.
     * @return string The sanitized request method.
     */
    private function method($method)
    {
        $method = strtoupper($method);

        return ($method === 'POST') ? $method : 'GET';
    }

    /**
     * Build a label tag <label></label>.
     *
     * @param string $for The 'for' attribute.
     * @param string $display The text to display.
     * @param array $attributes Extra attributes.
     * @return string
     */
    public function label($for, $display, array $attributes = array())
    {
        $merge = compact('for');

        $attributes = array_merge($attributes, $merge);

        return '<label '.$this->html->attributes($attributes).'>'.$display.'</label>';
    }

    /**
     * Build an input tag.
     *
     * @param string $type The input type attribute.
     * @param string $name The input name attribute.
     * @param null $value The input value attribute.
     * @param array $attributes Extra attributes to populate.
     * @return string An input html tag.
     */
    public function input($type, $name, $value = null, array $attributes = array())
    {
        $merge = compact('type', 'name', 'value');

        $attributes = array_merge($attributes, $merge);

        return '<input '.$this->html->attributes($attributes).'>';
    }

    /**
     * Build a text input <input type="text">
     * Note: the input are for HTML5 < +
     *
     * @param string $name The name attribute.
     * @param null $value The value to display.
     * @param array $attributes The extras attributes to add.
     * @return string
     */
    public function text($name, $value = null, array $attributes = array())
    {
        return $this->input('text', $name, $value, $attributes);
    }

    /**
     * Build a single email input <input type="email">
     *
     * @param string $name The name attribute.
     * @param null $value The value attribute.
     * @param array $attributes
     * @return string
     */
    public function email($name, $value = null, array $attributes = array())
    {
        if(!isset($attributes['placeholder'])){
            $attributes['placeholder'] = 'Please enter your email...';
        }

        return $this->input('email', $name, $value, $attributes);
    }

    /**
     * Build a single checkbox input <input type="checkbox">
     * Create your own method for multiple checkboxes.
     *
     * @param string $name The input name attribute.
     * @param string $value String value if single.
     * @param array $attributes Input extra attributes.
     * @return string
     */
    public function checkbox($name, $value = 'on', array $attributes = array())
    {
        // If checkbox value is 'on', show it checked.
        if('on' === $value){
            $attributes['checked'] = 'checked';
        }

        return $this->input('checkbox', $name, $value, $attributes);
    }

    /**
     * Build a group of checkbox.
     *
     * @param string $name The group name attribute.
     * @param array $choices The available choices.
     * @param array $value The checked values.
     * @param array $attributes
     * @return string
     */
    public function checkboxes($name, array $choices, array $value = array(), array $attributes = array())
    {
        return $this->makeGroupCheckableField('checkbox', $name, $choices, $value, $attributes);
    }

    /**
     * Build a group of radio input <input type="radio">
     *
     * @param string $name The input name attribute.
     * @param array $choices The radio field options.
     * @param array $value The input value. Muse be an array!
     * @param array $attributes
     * @return string
     */
    public function radio($name, array $choices, array $value = array(), array $attributes = array())
    {
        return $this->makeGroupCheckableField('radio', $name, $choices, $value, $attributes);
    }

    /**
     * Helper method to build checkbox or radio tag.
     *
     * @param string $type The type of the input.
     * @param string $name Name of the group.
     * @param array $choices The tag choice.
     * @param array $value The values of the group
     * @param array $attributes
     * @return string
     */
    private function makeGroupCheckableField($type, $name, array $choices, array $value, array $attributes)
    {
        $field = '';

        foreach($choices as $choice):

            // Check the value.
            // If checked, add the attribute.
            if(in_array($choice, $value)){
                $attributes['checked'] = 'checked';
            }

            // Build html output
            $field.= '<label>'.$this->input($type, $name.'[]', $choice, $attributes).ucfirst($choice).'</label>';

        endforeach;

        return $field;
    }

    /**
     * Build a textarea tag <textarea></textarea>
     *
     * @param string $name The name attribute.
     * @param null|string $value The content of the textarea.
     * @param array $attributes
     * @return string
     */
    public function textarea($name, $value = null, array $attributes = array())
    {
        $merge = compact('name');

        $attributes = array_merge($attributes, $merge);

        return '<textarea name="'.$name.'" '.$this->html->attributes($attributes).'>'.$value.'</textarea>';
    }

    /**
     * Build a select open tag <select>
     *
     * @param string $name The name attribute of the field.
     * @param array $options The options of the select tag.
     * @param null $value string if single, array if multiple enabled.
     * @param array $attributes
     * @return string
     */
    public function select($name, array $options = array(), $value = null, array $attributes = array())
    {
        $merge = compact('name');

        $attributes = array_merge($attributes, $merge);

        // Check if multiple is defined.
        // If defined, change the name attribute.
        if(isset($attributes['multiple']) && 'multiple' === $attributes['multiple']){
            $attributes['name'] = $attributes['name'].'[]';
        } else {
            unset($attributes['multiple']);
        }

        return '<select'.$this->html->attributes($attributes).'></select>';
    }

}