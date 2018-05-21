<?php

namespace Themosis\Forms\Fields\Types;

class TextType extends BaseType
{
    public function view()
    {
        // <input type="text" name="firstname" data-attr1="val1" ... value="joe">
        // <entity attribute="attr_value" attribute="attr_value" ...>
    }
}
