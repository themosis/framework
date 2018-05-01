<?php

namespace Themosis\Forms\Fields\Types;

use Themosis\Forms\Contracts\FieldTypeInterface;

class TextType extends BaseType implements FieldTypeInterface
{
    /**
     * Return the field type html.
     *
     * @return string
     */
    public function toHTML()
    {
        return '<input type="text"'.$this->attributes().'>';
    }
}
