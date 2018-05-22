<?php

namespace Themosis\Forms\Contracts;

interface FieldTypeInterface
{
    /**
     * Set the field options.
     *
     * @param array $options
     *
     * @return FieldTypeInterface
     */
    public function setOptions(array $options): FieldTypeInterface;

    /**
     * Get field type options.
     *
     * @param string $optionKey
     *
     * @return array
     */
    public function getOptions(string $optionKey = ''): array;

    /**
     * Set a field prefix. Mainly applied to field name to avoid conflict
     * with query variables.
     *
     * @param string $prefix
     *
     * @return FieldTypeInterface
     */
    public function setPrefix(string $prefix): FieldTypeInterface;

    /**
     * Return the field prefix.
     *
     * @return string
     */
    public function getPrefix(): string;

    /**
     * Return field attributes list.
     *
     * @return array
     */
    public function getAttributes();

    /**
     * Set the attributes for the field.
     *
     * @param array $attributes
     *
     * @return FieldTypeInterface
     */
    public function setAttributes(array $attributes);
}
