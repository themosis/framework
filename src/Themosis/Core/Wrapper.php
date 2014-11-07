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
            case 'select':

                $parsedValue = $this->parseArrayable($field, $value);
                break;

            case 'infinite':

                // Check for the registered fields and their default value if one.
                $parsedValue = $this->parseInfinite($field, $value);
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
     * @return string
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
     * Parse default value for fields using array values.
     *
     * @param FieldBuilder $field
     * @param array $value
     * @return array
     */
    private function parseArrayable(FieldBuilder $field, $value = array())
    {
        if (is_null($value) || empty($value))
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
     * @param FieldBuilder $field
     * @param array $value
     * @return array
     */
    private function parseInfinite(FieldBuilder $field, $value = array())
    {
        $fields = $field['fields'];

        // If null or empty, grab the only available array: the inner fields
        if (is_null($value) || empty($value))
        {
            // Set value as array.
            $value = array();

            foreach ($fields as $innerField)
            {
                $value[1][$innerField['name']] = $this->parseValue($innerField);
            }
        }
        else
        {
            $value = (array) $value;

            // Parse the values. If empty and a default is defined, the default value is applied.
            foreach ($value as $i => $rowValues)
            {
                foreach ($rowValues as $name => $val)
                {
                    // Get the associate field.
                    $f = $this->getInfiniteInnerField($fields, $name);

                    // Apply default value if empty and if default exists.
                    $value[$i][$name] = $this->parseValue($f, $val);
                }
            }
        }

        return (array) $value;
    }

    /**
     * Grab the inner field of an Infinite field.
     *
     * @param array $fields List of inner fields.
     * @param string $name Name of the inner field to fetch.
     * @return mixed The Field instance
     */
    private function getInfiniteInnerField(array $fields, $name)
    {
        foreach ($fields as $field)
        {
            if ($name === $field['name'])
            {
                return $field;
            }
        }
    }

} 