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
     * Return a PasswordField instance.
     *
     * @param string $name The name attribute of the password input.
     * @param array $extras Extra field properties.
     * @return \Themosis\Field\Fields\PasswordField
     */
    public function password($name, array $extras = array())
    {
        $properties = compact('name');

        $properties = array_merge($extras, $properties);

        return $this->make('Themosis\\Field\\Fields\\PasswordField', $properties);
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

    /**
     * Return a CheckboxesField instance.
     *
     * @param string $name The name attribute.
     * @param array $options The checkboxes options.
     * @param array $extras Extra field properties.
     * @return \Themosis\Field\Fields\CheckboxesField
     */
    public function checkboxes($name, array $options, array $extras = array())
    {
        $properties = compact('name', 'options');

        $properties = array_merge($extras, $properties);

        return $this->make('Themosis\\Field\\Fields\\CheckboxesField', $properties);
    }

    /**
     * Return a RadioField instance.
     *
     * @param string $name The name attribute.
     * @param array $options The radio options.
     * @param array $extras Extra field properties.
     * @return \Themosis\Field\Fields\RadioField
     */
    public function radio($name, array $options, array $extras = array())
    {
        $properties = compact('name', 'options');

        $properties = array_merge($extras, $properties);

        return $this->make('Themosis\\Field\\Fields\\RadioField', $properties);
    }

    /**
     * Define a SelectField instance.
     *
     * @param string $name The name attribute of the select custom field.
     * @param array $options The select options tag.
     * @param bool $multiple
     * @param array $extras
     * @return \Themosis\Field\Fields\SelectField
     */
    public function select($name, array $options, $multiple = false, array $extras = array())
    {
        $properties = compact('name', 'options');

        // Check the multiple attribute.
        if(true == $multiple){
            $properties['multiple'] = 'multiple';
        }

        $properties = array_merge($extras, $properties);

        return $this->make('Themosis\\Field\\Fields\\SelectField', $properties);
    }

    /**
     * Return a MediaField instance.
     *
     * @param string $name The name attribute of the hidden input.
     * @param array $extras Extra field properties.
     * @return \Themosis\Field\Fields\MediaField
     */
    public function media($name, array $extras = array())
    {
        $properties = compact('name');

        $properties = array_merge($extras, $properties);

        return $this->make('Themosis\\Field\\Fields\\MediaField', $properties);
    }

    /**
     * Define an InfiniteField instance.
     *
     * @param string $name The name attribute of the infinite inner inputs.
     * @param array $fields The fields to repeat.
     * @param array $extras
     * @return \Themosis\Field\Fields\InfiniteField
     */
    public function infinite($name, array $fields, array $extras = array())
    {
        $properties = compact('name', 'fields');

        $properties = array_merge($extras, $properties);

        return $this->make('Themosis\\Field\\Fields\\InfiniteField', $properties);
    }

    /**
     * Define an EditorField instance.
     * @link http://codex.wordpress.org/Function_Reference/wp_editor
     *
     * @param string $name The name attribute if the editor field.
     * @param array $settings The 'wp_editor' settings.
     * @param array $extras
     * @return \Themosis\Field\Fields\EditorField
     */
    public function editor($name, array $settings = array(), array $extras = array())
    {
        // $name may only contain lower-case characters.
        $name = strtolower($name);

        $properties = compact('name', 'settings');
        $properties = array_merge($extras, $properties);

        return $this->make('Themosis\\Field\\Fields\\EditorField', $properties);
    }

} 