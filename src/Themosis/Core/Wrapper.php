<?php
namespace Themosis\Core;

use Themosis\Field\Fields\FieldBuilder;

abstract class Wrapper {

    /**
     * Set a default value for a given field.
     *
     * @param FieldBuilder $field A field instance
     * @return mixed
     */
    protected function parseValue(FieldBuilder $field)
    {
        $value = null;

        // No data found, define a default by field type.
        switch($field->getFieldType()){

            case 'checkbox':

                $value = 'off';
                break;

            case 'checkboxes':
            case 'radio':
            case 'select':
            case 'infinite':

                $value = array();
                break;

            default:

                $value = '';

        }

        return $value;

    }

} 