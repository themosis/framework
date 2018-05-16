<?php

namespace Themosis\Forms\Fields\Types;

class NumberType extends BaseType
{
    /**
     * @return string
     */
    public function build()
    {
        // Handle the default value if one defined.
        if (! is_null($this->default) && is_numeric($this->default)) {
            $this['value'] = $this->default;
        }

        return '<input type="number"'.$this->attributes().'>';
    }
}
