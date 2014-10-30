<?php
namespace Themosis\Core;

use Themosis\Field\Fields\FieldBuilder;

abstract class Wrapper {

    /**
     * Set a default value for a given field.
     *
     * @param FieldBuilder $field A field instance.
     * @param mixed $value A registered value.
     * @return mixed
     */
    protected function parseValue(FieldBuilder $field, $value = null)
    {
        $parsedValue = null;

        // No data found, define a default by field type.
        switch($field->getFieldType()){

            case 'checkbox':

                $value = (string) $value;
                $parsedValue = $this->parseCheckbox($field, $value);
                break;

            case 'checkboxes':
            case 'radio':

                $parsedValue = $this->parseCheckables($field, $value);
                break;

            case 'select':

                $parsedValue = $this->parseSelect($field, $value);
                break;

            case 'infinite':

                // Check for the registered fields and their default value if one.
                $parsedValue = array();
                break;

            // Text
            // Textarea
            // Password
            // Media
            // Editor
            default:
                $parsedValue = $this->parseString($field, $value);

        }

        return $parsedValue;

    }

    /**
     * Parse default value for fields with string values.
     *
     * @param FieldBuilder $field The custom field instance.
     * @param string $value Value sent to the field.
     * @return string The field value.
     */
    private function parseString(FieldBuilder $field, $value = '')
    {
        return (empty($value) && isset($field['default'])) ? $field['default'] : $value;
    }

    /**
     * Parse default value for checkbox field.
     *
     * @param FieldBuilder $field
     * @param null $value
     * @return null|string
     */
    private function parseCheckbox(FieldBuilder $field, $value = null)
    {
        $val = null;

        // Check the defaults
        if (isset($field['default']))
        {
            if ($field['default'])
            {
                $val = 'on';
            }
            else
            {
                $val = 'off';
            }
        }

        // Check the given values
        if (is_null($value) || empty($value))
        {
            $val = 'off';
        }
        elseif ('on' === $value)
        {
            $val = 'on';
        }

        return $val;
    }

    /**
     * Parse default value for checkable fields.
     * @param FieldBuilder $field
     * @param array $value
     * @return array
     */
    private function parseCheckables(FieldBuilder $field, $value = array())
    {
        if (empty($value) || is_null($value))
        {
            if (isset($field['default']))
            {
                return (array) $field['default'];
            }

            return array();
        }

        return (array) $value;
    }

    /**
     * Parse default value for select fields.
     *
     * @param FieldBuilder $field
     * @param array $value
     * @return string|array
     */
    private function parseSelect(FieldBuilder $field, $value = array())
    {
        if (is_null($value) || empty($value))
        {
            if (isset($field['default']))
            {
                return (array) $field['default'];
            }

            return array();
        }

        return $value;
    }

} 