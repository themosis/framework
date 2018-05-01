<?php

namespace Themosis\Forms\Fields\Types;

class TextType extends BaseType
{
    /**
     * @return string
     */
    protected function build()
    {
        return '<input type="text"'.$this->attributes().'>';
    }
}
