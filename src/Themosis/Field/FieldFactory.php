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
     * @param string $class The custom field class name.
     * @param array $fieldProperties The defined field properties. Muse be an associative array.
     * @throws FieldException
     * @return object Themosis\Field\FieldBuilder
     */
    public function make($class, array $fieldProperties)
    {
        try{

            // Return the called class.
            $class =  new $class($fieldProperties);

        } catch(\Exception $e){

            //@TODO Implement log if class is not found

        }

        return $class;

    }

    /**
     * Return a TextField instance.
     *
     * @param string $name The name attribute of the text input.
     * @param array $extras Extra field properties.
     * @return \Themosis\Field\Fields\TextField
     */
    public function text($name, array $extras = array())
    {
        $properties = compact('name');

        $properties = array_merge($extras, $properties);

        return $this->make('Themosis\\Field\\Fields\\TextField', $properties);
    }

    /**
     * Return a TextareaField instance.
     *
     * @param string $name The name attribute of the textarea.
     * @param array $extras Extra field properties.
     * @return \Themosis\Field\Fields\TextareaField
     */
    public function textarea($name, array $extras = array())
    {
        $properties = compact('name');

        $properties = array_merge($extras, $properties);

        return $this->make('Themosis\\Field\\Fields\\TextareaField', $properties);
    }

    /**
     * Return a CheckboxField instance.
     *
     * @param string $name The name attribute of the checkbox input.
     * @param array $extras Extra field properties.
     * @return \Themosis\Field\Fields\CheckboxField
     */
    public function checkbox($name, array $extras = array())
    {
        $properties = compact('name');

        $properties = array_merge($extras, $properties);

        return $this->make('Themosis\\Field\\Fields\\CheckboxField', $properties);
    }

} 