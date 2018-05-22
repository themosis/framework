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

    /**
     * Set the form prefix. If fields are attached to the form,
     * all fields are updated with the given prefix.
     *
     * @param string $prefix
     *
     * @return FormInterface
     */
    public function setPrefix(string $prefix): FormInterface;

    /**
     * Return the form prefix.
     *
     * @return string
     */
    public function getPrefix(): string;

    /**
     * Return attached fields instances.
     *
     * @param string $name  The field base name.
     * @param string $group
     *
     * @return mixed
     */
    public function getFields(string $name = '', string $group = 'default');
}
