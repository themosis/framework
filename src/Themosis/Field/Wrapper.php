<?php

namespace Themosis\Field;

use Themosis\Field\Fields\IField;

abstract class Wrapper
{
    /**
     * Set a default value for a given field.
     *
     * @param IField $field A field instance.
     * @param mixed  $value A registered value.
     *
     * @return mixed
     */
    protected function parseValue(IField $field, $value = null)
    {
        $parsedValue = null;

        // No data found, define a default by field type.
        switch ($field->getFieldType()) {
            case 'checkbox':
            case 'radio':
            case 'select':
            case 'collection':

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
     * @param IField $field The custom field instance.
     * @param string $value Value sent to the field.
     *
     * @return string The field value.
     */
    protected function parseString(IField $field, $value = '')
    {
        $features = $field['features'];

        return (empty($value) && isset($features['default'])) ? $features['default'] : $value;
    }

    /**
     * Parse default value for fields using array values.
     *
     * @param IField $field
     * @param array  $value
     *
     * @return array
     */
    protected function parseArrayable(IField $field, $value = [])
    {
        $features = $field['features'];

        if (is_null($value) || ('0' !== $value && empty($value))) {
            if (isset($features['default'])) {
                return (array) $features['default'];
            }

            return [];
        }

        return (array) $value;
    }

    /**
     * @param IField $field
     * @param array  $value
     *
     * @return array
     */
    protected function parseInfinite(IField $field, $value = [])
    {
        $fields = $field['fields'];

        // If null or empty, grab the only available array: the inner fields
        if (is_null($value) || empty($value)) {
            // Set value as array.
            $value = [];

            foreach ($fields as $innerField) {
                $value[1][$innerField['name']] = $this->parseValue($innerField);
            }
        } else {
            $value = (array) $value;

            // Parse the values. If empty and a default is defined, the default value is applied.
            foreach ($value as $i => $rowValues) {
                foreach ($rowValues as $name => $val) {
                    // Get the associate field.
                    $f = $this->getInfiniteInnerField($fields, $name);

                    // Check if the field still exists...
                    // A value might still exists in database while the field might not.
                    if ($name !== $f['name']) {
                        return;
                    }

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
     * @param array  $fields List of inner fields.
     * @param string $name   Name of the inner field to fetch.
     *
     * @return mixed The Field instance
     */
    protected function getInfiniteInnerField(array $fields, $name)
    {
        foreach ($fields as $field) {
            if ($name === $field['name']) {
                return $field;
            }
        }
    }
}
