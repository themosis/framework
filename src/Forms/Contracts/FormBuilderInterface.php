<?php

namespace Themosis\Forms\Contracts;

interface FormBuilderInterface
{
    /**
     * Add a field to the current form instance.
     *
     *
     * @return $this
     */
    public function add(FieldTypeInterface $field): FormBuilderInterface;

    /**
     * Return generated form instance.
     */
    public function get(): FormInterface;
}
