<?php

namespace Themosis\Forms\Fields;

use Themosis\Forms\Contracts\FieldsRepositoryInterface;
use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Support\Contracts\SectionInterface;

class FieldsRepository implements FieldsRepositoryInterface
{
    /**
     * Fields organized by group.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * All groups with fields.
     *
     * @var SectionInterface[]
     */
    protected $groups = [];

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        return $this->fields;
    }

    /**
     * Add a field to the form instance.
     */
    public function addField(FieldTypeInterface $field, SectionInterface $group): FieldsRepositoryInterface
    {
        // We store all fields together
        // as well as per group. On each form,
        // there is a "default" group defined where
        // all fields are attached to. A user can specify
        // a form group to the passed options on the "add"
        // method of the FormBuilder instance.
        $this->fields[$field->getBaseName()] = $field;
        $this->groups[$group->getId()] = $group;

        return $this;
    }

    /**
     * Return the defined field instance based on its basename property.
     * If not set, return all fields from the "default" group.
     *
     *
     * @return FieldTypeInterface|FieldTypeInterface[]|array
     */
    public function getField(string $name = '', string $group = 'default')
    {
        /** @var SectionInterface $section */
        $section = $this->groups[$group];

        $foundItems = array_filter($section->getItems(), function (FieldTypeInterface $item) use ($name) {
            return $name === $item->getBaseName();
        });

        return ! empty($foundItems) ? array_shift($foundItems) : [];
    }

    /**
     * Retrieve a list of attached fields based
     * on provided group name.
     *
     *
     * @return SectionInterface|array
     */
    public function getFieldsByGroup(string $group = '')
    {
        return $this->groups[$group] ?? $this->groups;
    }

    /**
     * Return a list of registered groups within the form.
     */
    public function getGroups(): array
    {
        return $this->getFieldsByGroup();
    }

    /**
     * Retrieve a field by its name.
     */
    public function getFieldByName(string $name): ?FieldTypeInterface
    {
        return $this->fields[$name] ?? null;
    }

    /**
     * Check if form contains provided group instance (section).
     */
    public function hasGroup(string $name): bool
    {
        return isset($this->groups[$name]);
    }

    /**
     * Return the registered group/section instance.
     */
    public function getGroup(string $name): SectionInterface
    {
        return $this->groups[$name];
    }
}
