<?php

namespace Themosis\Field\Fields;

use Illuminate\View\Factory;

class RadioField extends FieldBuilder implements IField
{
    /**
     * Define a core CheckboxesField.
     *
     * @param array                    $properties The checkboxes field properties.
     * @param \Illuminate\View\Factory $view
     */
    public function __construct(array $properties, Factory $view)
    {
        parent::__construct($properties, $view);
        $this->fieldType();
    }

    /**
     * Method to override to define the input type
     * that handles the value.
     */
    protected function fieldType()
    {
        $this->type = 'radio';
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
        $this->defaultCheckableValue();

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

    /**
     * Handle the HTML code for taxonomy output.
     *
     * @return string
     */
    public function taxonomy()
    {
        return $this->metabox();
    }
}
