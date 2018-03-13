<?php

namespace Themosis\Field\Fields;

use Illuminate\View\Factory;

class EditorField extends FieldBuilder implements IField
{
    /**
     * Build an EditorField instance.
     *
     * @param array                    $properties
     * @param \Illuminate\View\Factory $view
     */
    public function __construct(array $properties, Factory $view)
    {
        parent::__construct($properties, $view);
        $this->fieldType();
    }

    /**
     * Set default settings for the WordPress editor.
     */
    protected function setSettings()
    {
        $settings = [
            'textarea_name' => $this['name'],
        ];

        $this['settings'] = isset($this['settings']) ? array_merge($settings, $this['settings']) : $settings;
    }

    /**
     * Define input where the value is saved.
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

        return $this->view->make('metabox._themosisEditorField', ['field' => $this])->render();
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

    /**
     * Handle the HTML code for user output.
     *
     * @return string
     */
    public function user()
    {
        return $this->metabox();
    }

    /**
     * Handle the HTML code for taxonomy output.
     *
     * @return string
     */
    public function taxonomy()
    {
        return $this->metabox();
    }
}
