<?php
namespace Themosis\Field\Fields;

use Themosis\Facades\Form;

class EditorField extends FieldBuilder{

    /**
     * Build an EditorField instance.
     *
     * @param array $properties
     */
    public function __construct(array $properties)
    {
        $this->properties = $properties;
        $this->fieldType();
        $this->setId();
        $this->setTitle();
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
     * Define input where the value is saved.
     *
     * @return void
     */
    protected function fieldType()
    {
        $this->type = 'textarea';
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

        // Start output buffer 'cause 'wp_editor' function is echoing its data.
        ob_start();
            wp_editor($this['value'], $this['name'], $this['settings']);
        $output .= ob_get_clean();

        if(isset($this['info'])){

            $output .= '<div class="themosis-field-info">';
            $output .= '<p>'.$this['info'].'</p></div>';

        }

        $output .= '</td></tr>';

        return $output;
    }
}