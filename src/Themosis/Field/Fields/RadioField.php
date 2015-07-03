<?php
namespace Themosis\Field\Fields;

use Themosis\View\ViewFactory;

class RadioField extends FieldBuilder implements IField
{
    /**
     * Define a core CheckboxesField.
     *
     * @param array $properties The checkboxes field properties.
     * @param ViewFactory $view
     */
    public function __construct(array $properties, ViewFactory $view)
    {
        parent::__construct($properties, $view);
        $this->fieldType();
    }

    /**
     * Method to override to define the input type
     * that handles the value.
     *
     * @return void
     */
    protected function fieldType()
    {
        $this->type = 'radio';
    }

    /**
     * Define a default value as array.
     * Checkboxes field accept only array as value.
     *
     * @return void
     */
    protected function defaultValue()
    {
        if (empty($this['value']) || is_string($this['value']))
        {
            $this['value'] = [];
        }
    }

    /**
     * Method that handle the field HTML code for
     * metabox output.
     *
     * @return string
     */
    public function metabox()
    {
        // If non existing values or if string sent,
        // define the default value for the field.
        $this->defaultValue();

        return $this->view->make('metabox._themosisRadioField', ['field' => $this])->render();
    }

    /**
     * Handle the field HTML code for the
     * Settings API output.
     *
     * @return string
     */
    public function page()
    {
        return $this->metabox();
    }

    /**
     * Handle the HTML code for user output.
     *
     * @return string
     */
    public function user()
    {
        return $this->metabox();
    }


} 