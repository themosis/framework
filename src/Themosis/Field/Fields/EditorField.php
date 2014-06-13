<?php
namespace Themosis\Field\Fields;

use Themosis\Facades\View;

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
     * Set default settings for the WordPress editor.
     *
     * @return void
     */
    private function setSettings()
    {
        $settings = array(
            'textarea_name' => $this['name']
        );

        $this['settings'] = isset($this['settings']) ? array_merge($settings, $this['settings']) : $settings;
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
        $this->setSettings();

        return View::make('metabox._themosisEditorField', array('field' => $this))->render();
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