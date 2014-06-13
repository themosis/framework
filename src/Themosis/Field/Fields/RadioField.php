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
        $this->properties = $properties;
        $this->setId();
        $this->setTitle();
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
     * Set a default ID attribute if not defined.
     *
     * @return void
     */
    private function setId()
    {
        $this['id'] = isset($this['id']) ? $this['id'] : $this['name'].'-id';
    }

    /**
     * Set a default label title, display text if not defined.
     *
     * @return void
     */
    private function setTitle()
    {
        $this['title'] = isset($this['title']) ? ucfirst($this['title']) : ucfirst($this['name']);
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