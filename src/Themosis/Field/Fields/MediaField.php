<?php
namespace Themosis\Field\Fields;

use Themosis\View\ViewFactory;

class MediaField extends FieldBuilder implements IField
{
    /**
     * Build a MediaField instance.
     *
     * @param array $properties
     * @param ViewFactory $view
     */
    public function __construct(array $properties, ViewFactory $view)
    {
        parent::__construct($properties, $view);
        $this->fieldType();
        $this->setType();
    }

    /**
     * Set the type data of the media to insert.
     * If no type is defined, default to 'image'.
     *
     * @return void
     */
    protected function setType()
    {
        $allowed = ['image', 'application', 'video', 'audio'];
        $features = $this['features'];

        if (isset($features['type']) && !in_array($features['type'], $allowed))
        {
            $features['type'] = 'image';
        }
        elseif (!isset($features['type']))
        {
            $features['type'] = 'image';
        }

        // Set the features back.
        $this['features'] = $features;

        // Set the data-type attribute.
        $atts = $this['atts'];
        $atts['data-type'] = $this['features']['type'];
        $this['atts'] = $atts;
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
    protected function setTitle()
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
        return $this->view->make('metabox._themosisMediaField', ['field' => $this])->render();
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


}