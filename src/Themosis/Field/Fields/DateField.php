<?php

namespace Themosis\Field\Fields;

use Illuminate\View\Factory;

class DateField extends FieldBuilder implements IField
{
    /**
     * Define a core TextField.
     *
     * @param array                    $properties The text field properties.
     * @param \Illuminate\View\Factory $view
     */
    public function __construct(array $properties, Factory $view)
    {
        parent::__construct($properties, $view);
        $this->fieldType();
    }

    /**
     * Method to override to define the input type
     * that handles the value.
     */
    protected function fieldType()
    {
        $this->type = 'date';
    }

    /**
     * Handle the field HTML code for metabox output.
     *
     * @return string
     */
    public function metabox()
    {
        return $this->view->make('metabox._themosisDateField', ['field' => $this])->render();
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
