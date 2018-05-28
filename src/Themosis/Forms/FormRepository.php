<?php

namespace Themosis\Forms;

use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Forms\Contracts\FormRepositoryInterface;
use Themosis\Support\Contracts\SectionInterface;

class FormRepository implements FormRepositoryInterface
{
    /**
     * Fields organized by group.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * All fields.
     *
     * @var FieldTypeInterface[]
     */
    protected $allFields = [];

    /**
     * The groups instances (sections).
     *
     * @var array
     */
    protected $groups = [];

    /**
     * @inheritdoc
     */
    public function all(): array
    {
        return $this->allFields;
    }

    /**
     * Add a field to the form instance.
     *
     * @param FieldTypeInterface $field
     * @param SectionInterface   $group
     *
     * @return FormRepositoryInterface
     */
    public function addField(FieldTypeInterface $field, SectionInterface $group): FormRepositoryInterface
    {
        // We store all fields together
        // as well as per group. On each form,
        // there is a "default" group defined where
        // all fields are attached to. A user can specify
        // a form group to the passed options on the "add"
        // method of the FormBuilder instance.
        $this->allFields[$field->getBaseName()] = $field;
        $this->fields[$group->getId()] = $group;

        return $this;
    }

    /**
     * Return the defined field instance based on its basename property.
     * If not set, return all fields from the "default" group.
     *
     * @param string $name
     * @param string $group
     *
     * @return FieldTypeInterface|FieldTypeInterface[]|array
     */
    public function getField(string $name = '', string $group = 'default')
    {
        /** @var SectionInterface $section */
        $section = $this->fields[$group];

        $foundItems = array_filter($section->getItems(), function (FieldTypeInterface $item) use ($name) {
            return $name === $item->getBaseName();
        });

        return ! empty($foundItems) ? array_shift($foundItems) : [];
    }

    /**
     * Retrieve a list of attached fields based
     * on provided group name.
     *
     * @param string $group
     *
     * @return array
     */
    public function getFieldsByGroup(string $group = ''): array
    {
        return $this->fields[$group] ?? $this->fields;
    }

    /**
     * Return a list of registered groups within the form.
     *
     * @return array
     */
    public function getGroups(): array
    {
        return array_keys($this->getFieldsByGroup());
    }

    /**
     * Retrieve a field by its name.
     *
     * @param string $name
     *
     * @return FieldTypeInterface
     */
    public function getFieldByName(string $name): FieldTypeInterface
    {
        return $this->allFields[$name] ?? null;
    }

    /**
     * Check if form contains provided group instance (section).
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasGroup(string $name): bool
    {
        return isset($this->groups[$name]);
    }

    /**
     * Return the registered group/section instance.
     *
     * @param string $name
     *
     * @return SectionInterface
     */
    public function getGroup(string $name): SectionInterface
    {
        return $this->groups[$name];
    }
}
