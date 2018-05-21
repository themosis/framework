<?php

namespace Themosis\Forms\Contracts;

interface FormInterface
{
    /**
     * Output the form as HTML.
     *
     * @return string
     */
    public function render(): string;

    /**
     * Attach a field to the form.
     *
     * @param FieldTypeInterface $field
     *
     * @return mixed
     */
    public function addField(FieldTypeInterface $field): FormInterface;
}
