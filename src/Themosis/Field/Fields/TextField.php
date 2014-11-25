<?php
namespace Themosis\Field\Fields;

use Themosis\Facades\View;

class TextField extends FieldBuilder{

    /**
     * Define a core TextField.
     *
     * @param array $properties The text field properties.
     */
    public function __construct(array $properties)
    {
        $this->properties = $properties;
        $this->setId();
        $this->setClass();
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
        $this->type = 'text';
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
     * Set a default class attribute if not defined.
     *
     * @return void
     */
    private function setClass()
    {
        $this['class'] = isset($this['class']) ? $this['class'] : 'large-text';
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
     * Handle the field HTML code for metabox output.
     *
     * @return string
     */
    public function metabox()
    {
        return View::make('metabox._themosisTextField', array('field' => $this))->render();
    }

    /**
     * Handle the field HTML code for the Settings API output.
     *
     * @return string
     */
    public function page()
    {
        return $this->metabox();
    }

}