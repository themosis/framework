<?php
namespace Themosis\Field\Fields;

use Themosis\Facades\View;

class PasswordField extends FieldBuilder implements IField
{
    /**
     * Define a core TextField.
     *
     * @param array $properties The text field properties.
     */
    public function __construct(array $properties)
    {
        parent::__construct($properties);

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
        $this->type = 'password';
    }

    /**
     * Handle the field HTML code for metabox output.
     *
     * @return string
     */
    public function metabox()
    {
        return View::make('metabox._themosisPasswordField', ['field' => $this])->render();
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


}