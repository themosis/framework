<?php
namespace Themosis\Field\Fields;

use Themosis\Facades\View;

class CheckboxField extends FieldBuilder{

    /**
     * Define a core CheckboxField.
     *
     * @param array $properties The checkbox field properties.
     */
    public function __construct(array $properties)
    {
        parent::__construct($properties);

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
        $this->type = 'checkbox';
    }

    /**
     * Method that handle the field HTML code for
     * metabox output.
     *
     * @return string
     */
    public function metabox()
    {
        return View::make('metabox._themosisCheckboxField', array('field' => $this))->render();
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
}