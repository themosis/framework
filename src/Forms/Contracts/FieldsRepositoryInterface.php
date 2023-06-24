<?php

namespace Themosis\Forms\Contracts;

use Themosis\Support\Contracts\SectionInterface;

interface FieldsRepositoryInterface
{
    /**
     * Return the list of all attached fields.
     */
    public function all(): array;

    /**
     * Attach a field to the form.
     */
    public function addField(FieldTypeInterface $field, SectionInterface $group): FieldsRepositoryInterface;

    /**
     * Return the defined field instance based on its basename property.
     * If not set, return all fields from the "default" group.
     *
     *
     * @return mixed
     */
    public function getField(string $name = '', string $group = 'default');

    /**
     * Retrieve a list of attached fields based
     * on provided group name.
     *
     *
     * @return SectionInterface|array
     */
    public function getFieldsByGroup(string $group = '');

    /**
     * Return a list of registered groups within the form.
     */
    public function getGroups(): array;

    /**
     * Retrieve a field by its name.
     */
    public function getFieldByName(string $name): ?FieldTypeInterface;

    /**
     * Check if form contains provided group instance (section).
     */
    public function hasGroup(string $name): bool;

    /**
     * Return the registered group/section instance.
     */
    public function getGroup(string $name): SectionInterface;
}
