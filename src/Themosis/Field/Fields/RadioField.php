<?php
namespace Themosis\Field\Fields;

use Themosis\Facades\View;

class RadioField extends FieldBuilder{

    /**
     * Define a core CheckboxesField.
     *
     * @param array $properties The checkboxes field properties.
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
        $this->type = 'radio';
    }

    /**
     * Define a default value as array.
     * Checkboxes field accept only array as value.
     *
     * @return void
     */
    private function defaultValue()
    {
        if(empty($this['value']) || is_string($this['value'])){
            $this['value'] = array();
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

        return View::make('metabox._themosisRadioField', array('field' => $this))->render();
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