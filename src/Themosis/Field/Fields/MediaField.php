<?php
namespace Themosis\Field\Fields;

use Themosis\Facades\Form;

class MediaField extends FieldBuilder {

    /**
     * Build a MediaField instance.
     *
     * @param array $properties
     */
    public function __construct(array $properties)
    {
        $this->properties = $properties;
        $this->setTitle();
    }

    /**
     * Define the input type that handle the data.
     *
     * @return void
     */
    protected function fieldType()
    {
        $this->type = 'hidden';
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
        $output = '<tr class="themosis-field-container themosis-field-media"><th class="themosis-label" scope="row">';
        $output .= Form::label($this['id'], $this['title']).'</th><td>';
        $output .= Form::hidden($this['name'], $this['value'], array('id' => 'themosis-media-input', 'data-type' => 'media'));
        $output .= '<table class="themosis-media"><tr>';

        // If a value exists, do not show the ADD button.
        if(!empty($this['value'])){
            $output .=  '<td class="themosis-media__buttons themosis-media--hidden">';
        } else {
            $output .= '<td class="themosis-media__buttons">';
        }

        $output .= '<button id="themosis-media-add" type="button" class="button button-primary">'.__('Add', THEMOSIS_TEXTDOMAIN).'</button>';
        $output .= '</td>';

        // If a value exists, show the DELETE button.
        if(!empty($this['value'])){
            $output .= '<td class="themosis-media__buttons">';
        } else {
            $output .= '<td class="themosis-media__buttons themosis-media--hidden">';
        }

        $output .= '<button id="themosis-media-delete" type="button" class="button">'.__('Delete', THEMOSIS_TEXTDOMAIN).'</button>';
        $output .= '</td>';

        // If a value exists, show the PATH
        if(!empty($this['value'])){
            $output .= '<td>';
        } else {
            $output .= '<td class="themosis-media--hidden">';
        }

        $output .= '<p class="themosis-media__path">'.$this['value'].'</p>';
        $output .= '</td>';
        $output .= '</tr></table>';

        if(isset($this['info'])){
            $output .= '<div class="themosis-field-info">';
            $output .= '<p>'.$this['info'].'</p></div>';
        }

        $output .= '</td></tr>';

        return $output;

    }
}