<?php
namespace Themosis\Core;

use Themosis\Field\Fields\FieldBuilder;

abstract class Wrapper {

    /**
     * Parse a wrapper value and returns it.
     *
     * @param array $request An associative array of values.
     * @param FieldBuilder $field A field instance
     * @return mixed
     */
    protected function parseValue(array $request, FieldBuilder $field)
    {
        $value = null;

        if(isset($request[$field['name']])){

            $value = $request[$field['name']];

        } else {

            // No data found, define a default by field type.
            switch($field->getFieldType()){

                case 'checkbox':

                    $value = 'off';

                    break;

                default:

                    $value = '';

            }

        }

        return $value;

    }

} 