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

    /**
     * Return field attributes list.
     *
     * @return array
     */
    public function getAttributes();

    /**
     * Set the attributes for the field.
     *
     * @param array $attributes
     *
     * @return FieldTypeInterface
     */
    public function setAttributes(array $attributes);
}
