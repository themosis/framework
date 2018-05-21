<?php

namespace Themosis\Forms\Contracts;

interface FormBuilderInterface
{
    /**
     * Add a field to the current form instance.
     *
     * @param string $name    The name attribute, model property.
     * @param string $type    The field type name.
     * @param array  $options Field options
     *
     * @return $this
     */
    public function add(string $name, string $type, array $options = []): FormBuilderInterface;

    /**
     * Return generated form instance.
     *
     * @return FormInterface
     */
    public function get(): FormInterface;
}
