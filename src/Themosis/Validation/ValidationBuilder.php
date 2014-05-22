<?php
namespace Themosis\Validation;

class ValidationBuilder {

    public function __construct()
    {

    }

    /**
     * Runs a validation rule on a single passed data.
     *
     * @param mixed $data The given data: string, int, array, bool...
     * @param array $rules The rules to use for validation.
     * @return mixed
     */
    public function single($data, array $rules)
    {
        foreach($rules as $rule){

            // Parse $rule and check for attributes.
            $ruleProperties = $this->parseRule($rule);

            // Set rule method.
            $signature = 'validate_'.$ruleProperties['rule'];

            // Check if the datas given is an array
            // If array, parse each item and return them
            // into the array.
            if(is_array($data)){

                // Overwrite each array value
                foreach($data as $key => $value){

                    // Validate the data value.
                    $data[$key] = $this->$signature($value, $ruleProperties['attributes']);

                }

            } else {

                // The data is a string or single value.
                $data = $this->$signature($data, $ruleProperties['attributes']);

            }

        }

        return $data;
    }

    /**
     * Parse validation rule and return an array containing the rule and its attributes.
     *
     * @param string $rule The validation rule to parse.
     * @return array
     */
    private function parseRule($rule)
    {
        $properties = array(
            'rule'          => '',
            'attributes'    => array()
        );

        // Check if attributes are defined...
        if(0 < strpos($rule, ':')){

            $extract = explode(':', $rule);

            // The rule
            $properties['rule'] = $extract[0];

            // The attributes
            $properties['attributes'] = $this->getAttributes($extract[1]);

        } else {

            // No attributes, simply defined the rule.
            // Leave attributes as empty array.
            $properties['rule'] = $rule;

        }

        return $properties;
    }

    /**
     * Return the defined attributes.
     *
     * @param string $attributes The string of attributes.
     * @return array
     */
    private function getAttributes($attributes)
    {
        // If comma, get a list of attributes
        if(0 < strpos($attributes, ',')){

            $attributes = explode(',', $attributes);
            $attributes = array_map(function($att){

                return trim($att);

            }, $attributes);

        } else {

            // No comma, only one attribute
            $attributes = array(trim($attributes));

        }

        return $attributes;
    }

    /**
     * Validate a value with only alphabetic characters.
     *
     * @param string $data The data to validate.
     * @param array $attributes
     * @return string
     */
    private function validate_alpha($data, array $attributes = array())
    {
        return ctype_alpha($data) ? $data : '';
    }

    /**
     * Validate a value with only numeric characters.
     *
     * @param string $data The data to validate.
     * @param array $attributes
     * @return string
     */
    private function validate_num($data, array $attributes = array())
    {
        return ctype_digit($data) ? $data : '';
    }

    /**
     * Validate a value with alphanumeric characters.
     *
     * @param string $data
     * @param array $attributes
     * @return string
     */
    private function validate_alnum($data, array $attributes = array())
    {
        return ctype_alnum($data) ? $data : '';
    }

    /**
     * Validate a text field value.
     *
     * @param string $data The data to validate.
     * @param array $attributes
     * @return string
     */
    private function validate_textfield($data, array $attributes = array())
    {
        return sanitize_text_field($data);
    }

    /**
     * Encode a textarea value.
     *
     * @param string $data
     * @param array $attributes
     * @return string
     */
    private function validate_textarea($data, array $attributes = array())
    {
        return esc_textarea($data);
    }

    /**
     * Encode a HTML value.
     *
     * @param string $data
     * @param array $attributes
     * @return string
     */
    private function validate_html($data, array $attributes = array())
    {
        return esc_html($data);
    }

    /**
     * Validate an email value.
     *
     * @param string $data The data to validate.
     * @param array $attributes
     * @return string
     */
    private function validate_email($data, array $attributes = array())
    {
        $email = sanitize_email($data);

        return is_email($email) ? $email : '';
    }

    /**
     * Validate a URL value.
     *
     * @param string $data The URL to validate.
     * @param array $attributes
     * @return string
     */
    private function validate_url($data, array $attributes = array())
    {
        if(!empty($attributes)){

            return esc_url($data, $attributes);

        }

        return esc_url($data);
    }

    /**
     * Validate a MIN length of string.
     *
     * @param string $data The string to evaluate.
     * @param array $attributes
     * @return string
     */
    private function validate_min($data, array $attributes = array())
    {
        // If no length defined, return empty string.
        // @TODO Log the lack of a length...
        if(empty($attributes)) return '';

        $length = $attributes[0];
        $data = trim($data);

        if($length <= strlen($data)){

            return $data;

        }

        return '';
    }

    /**
     * Validate a MAX length of string.
     *
     * @param string $data
     * @param array $attributes
     * @return string
     */
    private function validate_max($data, array $attributes = array())
    {
        // If no length defined, return empty string.
        // @TODO Log the lack of a length...
        if(empty($attributes)) return '';

        $length = $attributes[0];
        $data = trim($data);

        if($length >= strlen($data)){

            return $data;

        }

        return '';
    }

} 