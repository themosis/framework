<?php

namespace Themosis\Forms\Contracts;

interface FieldTypeInterface
{
    /**
     * Return the HTML output of the field.
     *
     * @param \Closure $callback
     *
     * @return string
     */
    public function toHTML(\Closure $callback = null);
}
