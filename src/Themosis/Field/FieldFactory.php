<?php
namespace Themosis\Field;

/**
 * Field factory.
 * @package Themosis\Field
 */
class FieldFactory {

    /**
     * Call the appropriate field class.
     *
     * @param string $type The class name to use: $class_Field.
     * @param array $fieldProperties The defined field properties. Muse be an associative array.
     * @throws FieldException
     * @return object Themosis\Field\FieldBuilder
     */
    public function make($type, array $fieldProperties)
    {
        // Only check for "CORE" field classes.
        $class = 'Themosis\\Field\\Fields\\'.ucfirst($type).'Field';

        // Return the called class.
        // @TODO Try-catch the class call. If errors, log it. (must implement log system)
        return new $class($fieldProperties);

    }

    /**
     * Return a Text_Field instance.
     *
     * @param string $name The name attribute of the text input.
     * @param array $extras Extra field properties.
     * @return \Themosis\Field\Fields\TextField
     */
    public function text($name, array $extras = array())
    {
        $properties = compact('name');

        $properties = array_merge($extras, $properties);

        return $this->make('text', $properties);
    }

} 