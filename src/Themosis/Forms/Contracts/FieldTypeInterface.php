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
     * @return mixed
     */
    public function getOptions(string $optionKey = '');

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
     * Return the field name property.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Return the field basename property.
     *
     * @return string
     */
    public function getBaseName(): string;

    /**
     * Return field attributes list.
     *
     * @return array
     */
    public function getAttributes();

    /**
     * Get the value of a defined attribute.
     *
     * @param string $name The attribute name.
     *
     * @return mixed
     */
    public function getAttribute(string $name);

    /**
     * Set the attributes for the field.
     *
     * @param array $attributes
     *
     * @return FieldTypeInterface
     */
    public function setAttributes(array $attributes);

    /**
     * Return a list of default options.
     *
     * @return array
     */
    public function getDefaultOptions(): array;

    /**
     * Return the allowed options for a field.
     *
     * @return array
     */
    public function getAllowedOptions(): array;
}
