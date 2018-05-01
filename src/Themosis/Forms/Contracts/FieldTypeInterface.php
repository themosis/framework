<?php

namespace Themosis\Forms\Contracts;

interface FieldTypeInterface
{
    /**
     * Return the HTML output of the field.
     *
     * @return string
     */
    public function toHTML();
}
