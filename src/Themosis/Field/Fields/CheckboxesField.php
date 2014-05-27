<?php
namespace Themosis\Field\Fields;

use Themosis\Facades\Form;

class CheckboxesField extends FieldBuilder {

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
    }

    /**
     * Method to override to define the input type
     * that handles the value.
     *
     * @return void
     */
    protected function fieldType()
    {
        $this->type = 'checkboxes';
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

        $output = '<tr class="themosis-field-container">';
        $output .= '<th class="themosis-label" scope="row">';
        $output .= Form::label($this['id'], $this['title']).'</th><td class="themosis-checkboxes">';
        $output .= Form::checkboxes($this['name'], $this['options'], $this['value'], array('data-field' => 'checkboxes'));

        if(isset($this['info'])){

            $output .= '<div class="themosis-field-info">';
            $output .= '<p>'.$this['info'].'</p></div>';

        }

        $output .= '</td></tr>';

        return $output;
    }

}