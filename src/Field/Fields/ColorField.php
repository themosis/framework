<?php

namespace Themosis\Field\Fields;

use Illuminate\View\Factory;

class ColorField extends FieldBuilder implements IField
{
    /**
     * @param array                    $properties
     * @param \Illuminate\View\Factory $view
     */
    public function __construct(array $properties, Factory $view)
    {
        parent::__construct($properties, $view);
        $this->fieldType();
    }

    /**
     * Override the input type that handles the value.
     */
    protected function fieldType()
    {
        $this->type = 'text';
    }

    /**
     * Handle the HTML code for metabox output.
     *
     * @return string
     */
    public function metabox()
    {
        return $this->view->make('metabox._themosisColorField', ['field' => $this])->render();
    }

    /**
     * Handle the HTML code for page/settings output.
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
