<?php

namespace Themosis\Forms\Fields\Types;

class TextType extends BaseType
{
    /**
     * @return string
     */
    protected function build()
    {
        // Handle the default value if one defined.
        if (! is_null($this->default) && is_string($this->default)) {
            $this['value'] = $this->default;
        }

        return '<input type="text"'.$this->attributes().'>';
    }
}
