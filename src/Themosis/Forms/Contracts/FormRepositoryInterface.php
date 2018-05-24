<?php

namespace Themosis\Forms\Contracts;

interface FormRepositoryInterface
{
    /**
     * Return the list of all attached fields.
     *
     * @return array
     */
    public function all(): array;

    /**
     * Attach a field to the form.
     *
     * @param FieldTypeInterface $field
     *
     * @return FormRepositoryInterface
     */
    public function addField(FieldTypeInterface $field): FormRepositoryInterface;

    /**
     * Return the defined field instance based on its basename property.
     * If not set, return all fields from the "default" group.
     *
     * @param string $name
     * @param string $group
     *
     * @return mixed
     */
    public function getField(string $name = '', string $group = 'default');

    /**
     * Retrieve a list of attached fields based
     * on provided group name.
     *
     * @param string $group
     *
     * @return array
     */
    public function getFieldsByGroup(string $group): array;
}
