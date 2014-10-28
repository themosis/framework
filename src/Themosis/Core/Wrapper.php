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

                $parsedValue = 'off';
                break;

            case 'checkboxes':
            case 'radio':
            case 'select':
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

} 