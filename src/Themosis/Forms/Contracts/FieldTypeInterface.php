<?php

namespace Themosis\Forms\Contracts;

use Illuminate\Contracts\Support\MessageBag;

interface FieldTypeInterface
{
    /**
     * Output the entity as HTML.
     *
     * @return string
     */
    public function render(): string;

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

    /**
     * Specify the view file to use by the entity.
     *
     * @param string $view
     *
     * @return FieldTypeInterface
     */
    public function setView(string $view): FieldTypeInterface;

    /**
     * Return the view instance used by the entity.
     *
     * @return string
     */
    public function getView(): string;

    /**
     * Indicates if the entity has been rendered or not.
     *
     * @return bool
     */
    public function isRendered(): bool;

    /**
     * Set the field transformer.
     *
     * @param DataTransformerInterface $transformer
     *
     * @return FieldTypeInterface
     */
    public function setTransformer(DataTransformerInterface $transformer): FieldTypeInterface;

    /**
     * Set the field raw value.
     *
     * @param string|array $value
     *
     * @return FieldTypeInterface
     */
    public function setValue($value): FieldTypeInterface;

    /**
     * Retrieve the field "normalized" value.
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Setup the field behavior.
     *
     * @return FieldTypeInterface
     */
    public function build(): FieldTypeInterface;

    /**
     * Return an error message bag instance.
     *
     * @return MessageBag
     */
    public function errors(): MessageBag;

    /**
     * Retrieve the error messages of a specific input.
     *
     * @param string $name
     * @param bool   $first
     *
     * @return string|array
     */
    public function error(string $name = '', bool $first = false);
}
