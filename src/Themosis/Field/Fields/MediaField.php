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
        $this->setType();
        $this->setSize();
        $this->fieldType();
    }

    /**
     * Set the type data of the media to insert.
     * If no type is defined, default to 'image'.
     *
     * @return void
     */
    private function setType()
    {
        $allowed = array('image', 'application', 'video', 'audio');

        if(isset($this['type']) && !in_array($this['type'], $allowed)){
            $this['type'] = 'image';
        } elseif(!isset($this['type'])){
            $this['type'] = 'image';
        }
    }

    /**
     * Set the size data of the media to insert.
     * If no size is defined, default to 'full'.
     *
     * @return void
     */
    private function setSize()
    {
        $sizes = get_intermediate_image_sizes();

        if(isset($this['size']) && !in_array($this['size'], $sizes)){
            $this['size'] = 'full';
        } elseif(!isset($this['size'])){
            $this['size'] = 'full';
        }
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
     * Handle the media field inner table HTML.
     *
     * @param string $value The field value.
     * @return string
     */
    private function mediaTable($value)
    {
        $output = '<table class="themosis-media"><tr>';

        // If a value exists, do not show the ADD button.
        if(!empty($value)){
            $output .=  '<td class="themosis-media__buttons themosis-media--hidden">';
        } else {
            $output .= '<td class="themosis-media__buttons">';
        }

        $output .= '<button id="themosis-media-add" type="button" class="button button-primary">'.__('Add', THEMOSIS_TEXTDOMAIN).'</button>';
        $output .= '</td>';

        // If a value exists, show the DELETE button.
        if(!empty($value)){
            $output .= '<td class="themosis-media__buttons">';
        } else {
            $output .= '<td class="themosis-media__buttons themosis-media--hidden">';
        }

        $output .= '<button id="themosis-media-delete" type="button" class="button">'.__('Delete', THEMOSIS_TEXTDOMAIN).'</button>';
        $output .= '</td>';

        // If a value exists, show the PATH
        if(!empty($value)){
            $output .= '<td>';
        } else {
            $output .= '<td class="themosis-media--hidden">';
        }

        $output .= '<p class="themosis-media__path">'.$value.'</p>';
        $output .= '</td>';
        $output .= '</tr></table>';

        return $output;
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
        $output .= Form::hidden($this['name'], $this['value'], array('id' => 'themosis-media-input', 'data-type' => $this['type'], 'data-size' => $this['size'], 'data-field' => 'media'));

        $output .= $this->mediaTable($this['value']);

        if(isset($this['info'])){
            $output .= '<div class="themosis-field-info">';
            $output .= '<p>'.$this['info'].'</p></div>';
        }

        $output .= '</td></tr>';

        return $output;

    }
}