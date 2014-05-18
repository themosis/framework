<?php
namespace Themosis\Field\Fields;

use Themosis\Facades\Form;

class CheckboxField extends FieldBuilder{

    /**
     * Define a core CheckboxField.
     *
     * @param array $properties The checkbox field properties.
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
        $this->type = 'checkbox';
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
        $output = '<tr class="themosis-field-container">';
        $output .= '<th class="themosis-label" scope="row">';
        $output .= Form::label($this['id'], $this['title']).'</th><td>';
        $output .= Form::checkbox($this['name'], $this['value'], array('id' => $this['id'], 'data-type' => 'checkbox'));

        if(isset($this['info'])){

            $output .= '<div class="themosis-field-info">';
            $output .= '<p>'.$this['info'].'</p></div>';

        }

        $output .= '</td></tr>';

        return $output;
    }
}