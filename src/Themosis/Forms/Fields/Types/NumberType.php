<?php

namespace Themosis\Forms\Fields\Types;

class NumberType extends BaseType
{
    /**
     * @return string
     */
    public function build()
    {
        return '<input type="number"'.$this->attributes().'>';
    }
}
