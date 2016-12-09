<?php

namespace Themosis\Html;

use Themosis\Foundation\Request;

class FormBuilder
{
    /**
     * An HtmlBuilder instance.
     *
     * @var HtmlBuilder
     */
    protected $html;

    /**
     * The request instance.
     *
     * @var \Themosis\Foundation\Request
     */
    protected $request;

    /**
     * Select tag options current index.
     * Note: only used with indexed (numeric) select options.
     * Used inside the "organizeOptions()" method to keep track
     * of option indexed/values.
     *
     * @var int
     */
    protected $option_current_index = 0;

    /**
     * Define a FormBuilder instance.
     *
     * @param HtmlBuilder                  $html
     * @param \Themosis\Foundation\Request $request
     */
    public function __construct(HtmlBuilder $html, Request $request)
    {
        $this->html = $html;
        $this->request = $request;
    }

    /**
     * Build opening tags for a form.
     *
     * @param string $action     The action attribute value.
     * @param string $method     The request method. Default to 'POST'.
     * @param bool   $ssl        Default value is false. True converts to https URL.
     * @param array  $attributes An array of form attributes.
     *
     * @return string The <form> open tag.
     */
    public function open($action = null, $method = 'POST', $ssl = false, array $attributes = [])
    {
        $attributes['action'] = $this->action($action, $ssl);
        $attributes['method'] = $this->method($method);

        // If a character encoding has not been specified in the attributes, we will
        // use the default encoding as specified in the application configuration
        // file for the "accept-charset" attribute.
        if (!array_key_exists('accept-charset', $attributes)) {
            $attributes['accept-charset'] = 'UTF-8';
        }

        // ADD NONCE FIELDS
        // IF 'POST' METHOD
        // HELP TO AVOID CSRF
        $append = '';
        $nonceAction = 'form';
        $nonceName = '_themosisnonce';

        // Replace custom nonce action.
        if (isset($attributes['nonce_action'])) {
            $nonceAction = $attributes['nonce_action'];
            unset($attributes['nonce_action']);
        }

        // Replace custom nonce name.
        if (isset($attributes['nonce'])) {
            $nonceName = $attributes['nonce'];
            unset($attributes['nonce']);
        }

        if ($attributes['method'] === 'POST') {
            $append = wp_nonce_field($nonceAction, $nonceName, true, false);
        }

        return '<form'.$this->html->attributes($attributes).'>'.$append;
    }

    /**
     * Build the closing tag.
     *
     * @return string The </form> close tag.
     */
    public function close()
    {
        return '</form>';
    }

    /**
     * Define the action attribute.
     *
     * @param string $action The action attribute value.
     * @param bool   $ssl    Tell to set the URL to https or not.
     *
     * @return string The converted action attribute value.
     */
    protected function action($action, $ssl)
    {
        $action = trim($action);
        $ssl = (bool) $ssl;

        // Check the given path.
        // If none given, set to the current page url.
        $uri = ($action === null || empty($action)) ? $this->request->getPathInfo() : '/'.trim($action, '/');

        // Build the action url.
        // The uri could be absolute or relative path.
        $action = $this->parseAction($uri, $ssl);

        return $action;
    }

    /**
     * Parse the action uri value.
     *
     * @param string $uri
     * @param bool   $ssl
     *
     * @return string
     */
    protected function parseAction($uri, $ssl)
    {
        if (strpos(esc_url($uri), 'http')) {
            $uri = esc_url($uri, ['http', 'https']);
            $uri = (starts_with($uri, '/')) ? substr($uri, 1) : $uri;

            return (is_ssl() || $ssl) ? str_replace('http://', 'https://', $uri) : $uri;
        }

        return (is_ssl() || $ssl) ? 'https://'.$this->request->getHttpHost().$uri : 'http://'.$this->request->getHttpHost().$uri;
    }

    /**
     * Define the form method attribute.
     *
     * @param string $method The request method.
     *
     * @return string The sanitized request method.
     */
    protected function method($method)
    {
        $method = strtoupper($method);

        return ($method === 'POST') ? $method : 'GET';
    }

    /**
     * Build a label tag <label></label>.
     *
     * @param string $display    The text to display.
     * @param array  $attributes Extra attributes.
     *
     * @return string
     */
    public function label($display, array $attributes = [])
    {
        return '<label'.$this->html->attributes($attributes).'>'.$display.'</label>';
    }

    /**
     * Build an input tag.
     *
     * @param string $type       The input type attribute.
     * @param string $name       The input name attribute.
     * @param null   $value      The input value attribute.
     * @param array  $attributes Extra attributes to populate.
     *
     * @return string An input html tag.
     */
    public function input($type, $name, $value = null, array $attributes = [])
    {
        $merge = compact('type', 'name', 'value');

        return '<input'.$this->html->attributes($merge).$this->html->attributes($attributes).'>';
    }

    /**
     * Build a text input <input type="text" />.
     *
     * @param string $name       The name attribute.
     * @param null   $value      The value to display.
     * @param array  $attributes The extras attributes to add.
     *
     * @return string
     */
    public function text($name, $value = null, array $attributes = [])
    {
        return $this->input('text', $name, $value, $attributes);
    }

    /**
     * Build a password input <input type="password" />.
     *
     * @param string $name       The name attribute.
     * @param string $value      The value attribute.
     * @param array  $attributes The extras attributes to add.
     *
     * @return string
     */
    public function password($name, $value = null, array $attributes = [])
    {
        return $this->input('password', $name, $value, $attributes);
    }

    /**
     * Build a single email input <input type="email" />.
     *
     * @param string $name       The name attribute.
     * @param string $value      The value attribute.
     * @param array  $attributes
     *
     * @return string
     */
    public function email($name, $value = null, array $attributes = [])
    {
        if (!isset($attributes['placeholder'])) {
            $attributes['placeholder'] = __('Please enter your email...', THEMOSIS_FRAMEWORK_TEXTDOMAIN);
        }

        return $this->input('email', $name, $value, $attributes);
    }

    /**
     * Build a number input <input type="number" />.
     *
     * @param string $name       The name attribute.
     * @param string $value      The input value.
     * @param array  $attributes
     *
     * @return string
     */
    public function number($name, $value = null, array $attributes = [])
    {
        return $this->input('number', $name, $value, $attributes);
    }

    /**
     * Build a date input <input type="date" />.
     *
     * @param string $name       The name attribute.
     * @param string $value      The input value.
     * @param array  $attributes
     *
     * @return string
     */
    public function date($name, $value = null, array $attributes = [])
    {
        return $this->input('date', $name, $value, $attributes);
    }

    /**
     * Build a single hidden input <input type="hidden" />.
     *
     * @param string $name       The name attribute.
     * @param null   $value      The value attribute.
     * @param array  $attributes
     *
     * @return string
     */
    public function hidden($name, $value = null, array $attributes = [])
    {
        return $this->input('hidden', $name, $value, $attributes);
    }

    /**
     * Build a single or multiple checkbox input <input type="checkbox" />.
     *
     * @param string       $name       The input name attribute.
     * @param string|array $choices    The available choices/acceptable values.
     * @param string|array $value      String value if single, array value if multiple.
     * @param array        $attributes Input extra attributes.
     *
     * @return string
     */
    public function checkbox($name, $choices, $value = '', array $attributes = [])
    {
        return $this->makeGroupCheckableField('checkbox', $name, (array) $choices, (array) $value, $attributes);
    }

    /**
     * Build a group of radio input <input type="radio">.
     *
     * @param string       $name       The input name attribute.
     * @param string|array $choices    The radio field options.
     * @param string|array $value      The input value. Muse be an array!
     * @param array        $attributes
     *
     * @return string
     */
    public function radio($name, $choices, $value = '', array $attributes = [])
    {
        return $this->makeGroupCheckableField('radio', $name, (array) $choices, (array) $value, $attributes);
    }

    /**
     * Helper method to build checkbox or radio tag.
     * The value is used to tell if the field is checked or not.
     * The true value attribute is defined by passed $choices.
     *
     * @param string $type       The type of the input.
     * @param string $name       Name of the group.
     * @param array  $choices    The tag choice.
     * @param array  $value      The values of the group
     * @param array  $attributes
     *
     * @return string
     */
    protected function makeGroupCheckableField($type, $name, array $choices, array $value, array $attributes)
    {
        $field = '';
        $labelAttributes = [];

        foreach ($choices as $choiceVal => $choice) {

            // The current choice that is parsed...
            $c = is_numeric($choiceVal) ? $choice : $choiceVal;

            // Check the value.
            // If it corresponds to the choice, add the checked attribute.
            // If checked, add the attribute.
            if (in_array($c, $value)) {
                array_push($attributes, 'checked');
            }

            // Check if there are label attributes defined.
            if (isset($attributes['label'])) {
                $labelAttributes = (array) $attributes['label'];
                unset($attributes['label']);
            }

            // Set the name attribute.
            // Check if there are multiple options. Radio input are always single as there is only one value selected.
            // But checkbox could be one or multiple. Only specify the name attribute as array if there more than one choice.
            if (count($choices) > 1 && 'radio' !== $type) {
                $n = $name.'[]';
            } else {
                $n = $name;
            }

            // Build html output
            $input = $this->input($type, $n, $c, $attributes).ucfirst($choice);
            $field .= $this->label($input, $labelAttributes);

            // Reset 'checked' attributes.
            $key = array_search('checked', $attributes);
            if (false !== $key) {
                unset($attributes[$key]);
            }
        }

        return $field;
    }

    /**
     * Build a textarea tag <textarea></textarea>.
     *
     * @param string      $name       The name attribute.
     * @param null|string $value      The content of the textarea.
     * @param array       $attributes
     *
     * @return string
     */
    public function textarea($name, $value = null, array $attributes = [])
    {
        $merge = compact('name');

        $attributes = array_merge($attributes, $merge);

        return '<textarea name="'.$name.'" '.$this->html->attributes($attributes).'>'.$value.'</textarea>';
    }

    /**
     * Build a select open tag <select>.
     *
     * @param string $name       The name attribute of the field.
     * @param array  $options    The options of the select tag.
     * @param null   $value      string if single, array if multiple enabled.
     * @param array  $attributes
     *
     * @return string
     */
    public function select($name, array $options = [], $value = null, array $attributes = [])
    {
        $merge = compact('name');

        $attributes = array_merge($attributes, $merge);

        // Check if multiple is defined.
        // If defined, change the name attribute.
        if (in_array('multiple', $attributes) || (isset($attributes['multiple']) && 'multiple' === $attributes['multiple'])) {
            $attributes['name'] = $attributes['name'].'[]';
        } else {
            unset($attributes['multiple']);
        }

        // Build the options of the select tag.
        $options = $this->makeOptionTags($options, $value);

        return '<select'.$this->html->attributes($attributes).'>'.$options.'</select>';
    }

    /**
     * Define inner option tags for the select tag.
     *
     * @param array $options The option fields to output.
     * @param mixed $value   Array if multiple, string if single.
     *
     * @return string
     */
    protected function makeOptionTags(array $options, $value)
    {
        $output = '';

        $options = $this->parseSelectOptions($options);

        // Start looping through the options.
        foreach ($options as $key => $option) {
            // Check the $key. If $key is a string, then we are dealing
            // with <optgroup> tags.
            if (is_string($key)) {
                $output .= $this->buildOptgroupTags($key, $option, $value);
            } else {
                // No <optgroup> tags, $key is int.
                $output .= $this->parseOptionTags($option, $value);
            }
        }
        // End options loop.

        return $output;
    }

    /**
     * Parse the options and re-order optgroup options if no custom keys defined.
     *
     * @param array $options The select tag options.
     *
     * @throws HtmlException
     *
     * @return array The parsed options.
     */
    protected function parseSelectOptions(array $options)
    {
        $parsedOptions = [];
        $sequential_index = 0;

        foreach ($options as $key => $option) {
            // Check $option is array in order to continue.
            if (!is_array($option)) {
                throw new HtmlException('In order to build the select tag, the parameter must be an array of arrays.');
            }

            // Re-order <optgroup> options
            if (is_string($key)) {
                $parsedOptions[$key] = $this->organizeOptions($options, $option);
            } else {
                // We're working with array of array for numeric indexes.
                // We check if the passed array is sequential or not.
                // If sequential, we need to re-index values from passed arrays.
                if (array_is_sequential($option)) {
                    foreach ($option as $val => $name) {
                        $parsedOptions[$key][$sequential_index] = $name;
                        $sequential_index++;
                    }
                } else {
                    // Else we have custom numeric index (post ids for example) or
                    // index as string (associative array), so we just assign them
                    // in their coming order.
                    $parsedOptions[$key] = $option;
                }
            }
        }

        return $parsedOptions;
    }

    /**
     * Re-order/re-index <optgroup> options.
     *
     * @param array $options    The select tag options(all).
     * @param array $subOptions The optgroup options.
     *
     * @return array
     */
    protected function organizeOptions(array $options, array $subOptions)
    {
        $indexedOptions = [];
        $convertedOptions = [];

        // Build the re-indexed options array.
        foreach ($options as $group) {
            foreach ($group as $i => $value) {
                // Check if sequential or not.
                if (array_is_sequential($group)) {
                    // Int values - Reorder options so there are
                    // no duplicates.
                    array_push($indexedOptions, $value);
                } else {
                    // Custom index or defined numeric indexes.
                    // Nothing to change.
                    $indexedOptions[$i] = $value;
                }
            }
        }

        // Grab the converted values and return them.
        foreach ($indexedOptions as $index => $option) {
            if (is_numeric($index)) {
                if (in_array($option, $subOptions) && !in_array($option, $convertedOptions) && $index >= $this->option_current_index) {
                    $convertedOptions[$index] = $option;

                    // Record index for comparison.
                    $this->option_current_index = $index;
                }
            } else {
                if (array_key_exists($index, $subOptions)) {
                    $convertedOptions[$index] = $option;
                }
            }
        }

        return $convertedOptions;
    }

    /**
     * Build the option group tag <optgroup></optgroup>.
     *
     * @param string $label   The tag label attribute.
     * @param array  $options The options to add to the group.
     * @param mixed  $value   See makeOptionTags method.
     *
     * @return string
     */
    protected function buildOptgroupTags($label, array $options, $value)
    {
        $options = $this->parseOptionTags($options, $value);

        return '<optgroup label="'.ucfirst($label).'">'.$options.'</optgroup>';
    }

    /**
     * Prepare select tag options for output.
     *
     * @param array $options The option values.
     * @param mixed $value   Array if multiple, string if single.
     *
     * @return string
     */
    protected function parseOptionTags(array $options, $value)
    {
        $output = '';

        foreach ($options as $key => $option) {
            $attributes = [];
            $selected = $this->setSelectable($key, $value);

            // Check if option tag has attributes defined.
            if (is_array($option)) {
                $attributes = isset($option['atts']) ? $option['atts'] : [];
                $option = isset($option['text']) ? $option['text'] : '';
            }

            // Build the option tag.
            $output .= $this->makeOptionTag($key, $option, $selected, $attributes);
        }

        return $output;
    }

    /**
     * Build an option tag <option></option>.
     *
     * @param mixed  $key        String if custom "value", otherwise int.
     * @param string $option     Option name to display.
     * @param string $selected   The selected attribute.
     * @param array  $attributes The custom attributes for the option tag.
     *
     * @return string
     */
    protected function makeOptionTag($key, $option, $selected = null, array $attributes = [])
    {
        // Do not allow to modify the value attribute through the $attributes array.
        if (isset($attributes['value'])) {
            unset($attributes['value']);
        }

        // Do not allow to modify the selected attribute through the $attributes array.
        if (isset($attributes['selected'])) {
            unset($attributes['selected']);
        }

        return '<option value="'.$key.'" '.$selected.' '.$this->html->attributes($attributes).'>'.ucfirst($option).'</option>';
    }

    /**
     * Define the selected attribute of an option tag.
     *
     * @param string $key   The option tag value.
     * @param mixed  $value The retrieved value. Array if multiple, string if single.
     *
     * @return string
     */
    protected function setSelectable($key, $value)
    {
        $selected = 'selected="selected"';
        // Deal if multiple selection.
        if (is_array($value) && in_array($key, $value)) {
            return $selected;
        }

        // Deal single selection.
        // $key might be an int or a string
        if (is_string($value) && $key == $value) {
            return $selected;
        }

        return '';
    }

    /**
     * Output a <button type="button"> tag.
     *
     * @param string $name       The tag name attribute.
     * @param string $display    The button display text.
     * @param array  $attributes Other tag attributes.
     *
     * @return string
     */
    public function button($name, $display = null, array $attributes = [])
    {
        return $this->makeButton('button', $name, $display, $attributes);
    }

    /**
     * @param string $name       The tag name attribute.
     * @param null   $display    The button display text.
     * @param array  $attributes Other tag attributes.
     *
     * @return string
     */
    public function submit($name, $display = null, array $attributes = [])
    {
        return $this->makeButton('submit', $name, $display, $attributes);
    }

    /**
     * Build a <button> tag.
     *
     * @param string $type       The button type attribute.
     * @param string $name       The button name attribute.
     * @param string $display    The button display text.
     * @param array  $attributes Other tag attributes.
     *
     * @return string
     */
    protected function makeButton($type, $name, $display = null, array $attributes = [])
    {
        $merge = compact('type', 'name');

        $attributes = array_merge($attributes, $merge);

        return '<button '.$this->html->attributes($attributes).'>'.$display.'</button>';
    }
}
