<?php

namespace Themosis\Field;

use Illuminate\View\Factory;

/**
 * Field factory.
 */
class FieldFactory
{
    /**
     * A view instance.
     *
     * @var \Illuminate\View\Factory
     */
    protected $view;

    /**
     * Define a FieldFactory instance.
     *
     * @param \Illuminate\View\Factory $view A view instance.
     */
    public function __construct(Factory $view)
    {
        $this->view = $view;
    }

    /**
     * Call the appropriate field class.
     *
     * @param string $class           The custom field class name.
     * @param array  $fieldProperties The defined field properties. Muse be an associative array.
     *
     * @throws FieldException
     *
     * @return object Themosis\Field\Fields\IField
     */
    public function make($class, array $fieldProperties)
    {
        try {
            // Return the called class.
            $class = new $class($fieldProperties, $this->view);
        } catch (\Exception $e) {
            //@TODO Implement log if class is not found
        }

        return $class;
    }

    /**
     * Return a TextField instance.
     *
     * @param string $name       The name attribute of the text input.
     * @param array  $features   Custom field features - title, info.
     * @param array  $attributes Input html attributes.
     *
     * @return \Themosis\Field\Fields\TextField
     */
    public function text($name, array $features = [], array $attributes = [])
    {
        $properties = [
            'features' => $features,
            'atts' => array_merge(['class' => 'large-text'], $attributes, ['data-field' => 'text']),
            'name' => $name,
        ];

        return $this->make('Themosis\\Field\\Fields\\TextField', $properties);
    }

    /**
     * Return a PasswordField instance.
     *
     * @param string $name       The name attribute of the password input.
     * @param array  $features   Custom field features - title, info.
     * @param array  $attributes Input html attributes.
     *
     * @return \Themosis\Field\Fields\PasswordField
     */
    public function password($name, array $features = [], array $attributes = [])
    {
        $properties = [
            'features' => $features,
            'atts' => array_merge(['class' => 'large-text'], $attributes, ['data-field' => 'password']),
            'name' => $name,
        ];

        return $this->make('Themosis\\Field\\Fields\\PasswordField', $properties);
    }

    /**
     * Return a NumberField instance.
     *
     * @param string $name       The name attribute of the number input.
     * @param array  $features   Custom field features - title, info.
     * @param array  $attributes Input html attributes.
     *
     * @return \Themosis\Field\Fields\NumberField
     */
    public function number($name, array $features = [], array $attributes = [])
    {
        $properties = [
            'features' => $features,
            'atts' => array_merge(['class' => 'small-text'], $attributes, ['data-field' => 'number']),
            'name' => $name,
        ];

        return $this->make('Themosis\\Field\\Fields\\NumberField', $properties);
    }

    /**
     * Return a DateField instance.
     *
     * @param string $name       The name attribute of the date input.
     * @param array  $features   Custom field features - title, info.
     * @param array  $attributes Input html attributes.
     *
     * @return \Themosis\Field\Fields\DateField
     */
    public function date($name, array $features = [], array $attributes = [])
    {
        $properties = [
            'features' => $features,
            'atts' => array_merge(['class' => 'newtag'], $attributes, ['data-field' => 'date']),
            'name' => $name,
        ];

        return $this->make('Themosis\\Field\\Fields\\DateField', $properties);
    }

    /**
     * Return a TextareaField instance.
     *
     * @param string $name       The name attribute of the textarea.
     * @param array  $features   Custom field features - title, info.
     * @param array  $attributes Input html attributes.
     *
     * @return \Themosis\Field\Fields\TextareaField
     */
    public function textarea($name, array $features = [], array $attributes = [])
    {
        $properties = [
            'features' => $features,
            'atts' => array_merge(['class' => 'large-text', 'rows' => 5], $attributes, ['data-field' => 'textarea']),
            'name' => $name,
        ];

        return $this->make('Themosis\\Field\\Fields\\TextareaField', $properties);
    }

    /**
     * Return a CheckboxField instance.
     *
     * @param string       $name       The name attribute of the checkbox input.
     * @param string|array $options    The checkbox options.
     * @param array        $features   Custom field features - title, info.
     * @param array        $attributes Input html attributes.
     *
     * @return \Themosis\Field\Fields\CheckboxField
     */
    public function checkbox($name, $options, array $features = [], array $attributes = [])
    {
        $properties = [
            'features' => $features,
            'atts' => array_merge($attributes, ['data-field' => 'checkbox']),
            'name' => $name,
            'options' => $options,
        ];

        return $this->make('Themosis\\Field\\Fields\\CheckboxField', $properties);
    }

    /**
     * Return a RadioField instance.
     *
     * @param string       $name       The name attribute.
     * @param string|array $options    The radio options.
     * @param array        $features   Custom field features - title, info.
     * @param array        $attributes Input html attributes.
     *
     * @return \Themosis\Field\Fields\RadioField
     */
    public function radio($name, $options, array $features = [], array $attributes = [])
    {
        $properties = [
            'features' => $features,
            'atts' => array_merge($attributes, ['data-field' => 'radio']),
            'name' => $name,
            'options' => $options,
        ];

        return $this->make('Themosis\\Field\\Fields\\RadioField', $properties);
    }

    /**
     * Define a SelectField instance.
     *
     * @param string $name       The name attribute of the select custom field.
     * @param array  $options    The select options tag.
     * @param array  $features   Custom field features - title, info.
     * @param array  $attributes Input html attributes.
     *
     * @return \Themosis\Field\Fields\SelectField
     */
    public function select($name, array $options, array $features = [], array $attributes = [])
    {
        $properties = [
            'features' => $features,
            'atts' => array_merge($attributes, ['data-field' => 'select']),
            'name' => $name,
            'options' => $options,
        ];

        return $this->make('Themosis\\Field\\Fields\\SelectField', $properties);
    }

    /**
     * Return a MediaField instance.
     *
     * @param string $name     The name attribute of the hidden input.
     * @param array  $features Custom field features - title, info, type (image, application, audio, video)
     *
     * @return \Themosis\Field\Fields\MediaField
     */
    public function media($name, array $features = [])
    {
        $properties = [
            'features' => $features,
            'atts' => ['class' => 'themosis-media-input', 'data-field' => 'media'],
            'name' => $name,
        ];

        return $this->make('Themosis\\Field\\Fields\\MediaField', $properties);
    }

    /**
     * Define an InfiniteField instance.
     *
     * @param string $name     The name attribute of the infinite inner inputs.
     * @param array  $fields   The fields to repeat.
     * @param array  $features Custom field features - title, info, limit.
     *
     * @return \Themosis\Field\Fields\InfiniteField
     */
    public function infinite($name, array $fields, array $features = [])
    {
        $properties = [
            'features' => $features,
            'fields' => $fields,
            'name' => $name,
        ];

        return $this->make('Themosis\\Field\\Fields\\InfiniteField', $properties);
    }

    /**
     * Define an EditorField instance.
     *
     * @link http://codex.wordpress.org/Function_Reference/wp_editor
     *
     * @param string $name     The name attribute if the editor field.
     * @param array  $features Custom field features - title, info.
     * @param array  $settings The 'wp_editor' settings.
     *
     * @return \Themosis\Field\Fields\EditorField
     */
    public function editor($name, array $features = [], array $settings = [])
    {
        $properties = [
            'features' => $features,
            'settings' => $settings,
            'name' => strtolower($name), // $name may only contain lower-case characters.
        ];

        return $this->make('Themosis\\Field\\Fields\\EditorField', $properties);
    }

    /**
     * Define a CollectionField instance.
     *
     * @param string $name     The name attribute.
     * @param array  $features Custom field features - title, info, type, limit.
     *
     * @return \Themosis\Field\Fields\CollectionField
     */
    public function collection($name, array $features = [])
    {
        $properties = [
            'features' => $features,
            'name' => $name,
        ];

        return $this->make('Themosis\\Field\\Fields\\CollectionField', $properties);
    }

    /**
     * Define a ColorField instance.
     *
     * @param string $name       The name attribute.
     * @param array  $features   Custom field features - title, info.
     * @param array  $attributes Input html attributes.
     *
     * @return \Themosis\Field\Fields\ColorField
     */
    public function color($name, array $features = [], array $attributes = [])
    {
        $properties = [
            'features' => $features,
            'atts' => array_merge($attributes, ['class' => 'themosis-color-field', 'data-field' => 'text']),
            'name' => $name,
        ];

        return $this->make('Themosis\\Field\\Fields\\ColorField', $properties);
    }
}
