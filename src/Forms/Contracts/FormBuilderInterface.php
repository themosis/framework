<?php

namespace Themosis\Forms\Contracts;

interface FormBuilderInterface
{
    /**
     * Add a field to the current form instance.
     *
     * @param FieldTypeInterface $field
     *
     * @return $this
     */
    public function add(FieldTypeInterface $field): FormBuilderInterface;

    /**
     * Return generated form instance.
     *
     * @return FormInterface
     */
    public function get(): FormInterface;
}
