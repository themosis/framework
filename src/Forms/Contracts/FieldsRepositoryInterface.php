<?php

namespace Themosis\Forms\Contracts;

use Themosis\Support\Contracts\SectionInterface;

interface FieldsRepositoryInterface
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
     * @param SectionInterface   $group
     *
     * @return FieldsRepositoryInterface
     */
    public function addField(FieldTypeInterface $field, SectionInterface $group): FieldsRepositoryInterface;

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
     * @return SectionInterface|array
     */
    public function getFieldsByGroup(string $group = '');

    /**
     * Return a list of registered groups within the form.
     *
     * @return array
     */
    public function getGroups(): array;

    /**
     * Retrieve a field by its name.
     *
     * @param string $name
     *
     * @return FieldTypeInterface
     */
    public function getFieldByName(string $name): FieldTypeInterface;

    /**
     * Check if form contains provided group instance (section).
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasGroup(string $name): bool;

    /**
     * Return the registered group/section instance.
     *
     * @param string $name
     *
     * @return SectionInterface
     */
    public function getGroup(string $name): SectionInterface;
}
