<?php

namespace Themosis\Forms\Contracts;

interface FormBuilderInterface
{
    /**
     * Add a field to the current form instance.
     *
     * @param FieldTypeInterface $field
     * @param array              $options Field options
     *
     * @return $this
     */
    public function add(FieldTypeInterface $field, array $options = []): FormBuilderInterface;

    /**
     * Return generated form instance.
     *
     * @return FormInterface
     */
    public function get(): FormInterface;
}
