<?php

namespace Themosis\Forms\Contracts;

use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Contracts\View\Factory as ViewFactoryInterface;
use League\Fractal\Manager;
use Themosis\Forms\Resources\Factory;

interface FieldTypeInterface
{
    /**
     * Output the entity as HTML.
     */
    public function render(): string;

    /**
     * Set the field options.
     */
    public function setOptions(array $options): FieldTypeInterface;

    /**
     * Return field options.
     *
     * @param  array  $excludes
     */
    public function getOptions(array $excludes = null): array;

    /**
     * Get field type option defined by key.
     *
     * @param  mixed  $default
     * @return string|array|null
     */
    public function getOption(string $key, $default = null);

    /**
     * Set a field prefix. Mainly applied to field name to avoid conflict
     * with query variables.
     */
    public function setPrefix(string $prefix): FieldTypeInterface;

    /**
     * Return the field prefix.
     */
    public function getPrefix(): string;

    /**
     * Return the field name property.
     */
    public function getName(): string;

    /**
     * Return the field basename property.
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
     * @param  string  $name The attribute name.
     * @return mixed
     */
    public function getAttribute(string $name);

    /**
     * Set the attributes for the field.
     *
     *
     * @return FieldTypeInterface
     */
    public function setAttributes(array $attributes);

    /**
     * Add an attribute to the field.
     *
     * @param  bool  $overwrite By default, it appends the value. Set to true, to replace the existing attribute value.
     */
    public function addAttribute(string $name, string $value, $overwrite = false): FieldTypeInterface;

    /**
     * Return a list of default options.
     */
    public function getDefaultOptions(): array;

    /**
     * Return the allowed options for a field.
     */
    public function getAllowedOptions(): array;

    /**
     * Specify the view file to use by the entity.
     */
    public function setView(string $view): FieldTypeInterface;

    /**
     * Set the field view factory instance.
     */
    public function setViewFactory(ViewFactoryInterface $factory): FieldTypeInterface;

    /**
     * Return the view instance used by the entity.
     */
    public function getView(bool $prefixed = true): string;

    /**
     * Indicates if the entity has been rendered or not.
     */
    public function isRendered(): bool;

    /**
     * Check if submitted form is valid.
     */
    public function isValid(): bool;

    /**
     * Check if submitted form is not valid.
     */
    public function isNotValid(): bool;

    /**
     * Set the field transformer.
     */
    public function setTransformer(DataTransformerInterface $transformer): FieldTypeInterface;

    /**
     * Set the field raw value.
     *
     * @param  string|array  $value
     */
    public function setValue($value, bool $shouldNotBypassTransformer = true): FieldTypeInterface;

    /**
     * Retrieve the field "normalized" value.
     *
     * @param  mixed  $default
     * @return mixed
     */
    public function getValue($default = null);

    /**
     * Retrieve the field original value (reverse transformed).
     *
     * @return mixed
     */
    public function getRawValue();

    /**
     * Return an error message bag instance.
     */
    public function errors(): MessageBag;

    /**
     * Retrieve the error messages of a specific input.
     *
     *
     * @return string|array
     */
    public function error(string $name = '', bool $first = false);

    /**
     * Return the field locale.
     */
    public function getLocale(): string;

    /**
     * Set the field locale.
     */
    public function setLocale(string $locale): FieldTypeInterface;

    /**
     * Return the field theme.
     */
    public function getTheme(): string;

    /**
     * Set the field theme.
     */
    public function setTheme(string $theme): FieldTypeInterface;

    /**
     * Return the Fractal manager.
     */
    public function getManager(): Manager;

    /**
     * Set the Fractal manager.
     */
    public function setManager(Manager $manager): FieldTypeInterface;

    /**
     * Return the transformer factory.
     */
    public function getResourceTransformerFactory(): Factory;

    /**
     * Set the transformer factory.
     */
    public function setResourceTransformerFactory(Factory $factory): FieldTypeInterface;

    /**
     * Return the resource transformer.
     */
    public function getResourceTransformer(): string;

    /**
     * Return a JSON representation of the field.
     */
    public function toJson(): string;

    /**
     * Return an associative array representation of the field.
     */
    public function toArray(): array;

    /**
     * Return the field type.
     */
    public function getType(): string;

    /**
     * Return the field component name.
     */
    public function getComponent(): string;

    /**
     * Pass custom data to the field view.
     *
     * @param  string|array  $key
     * @param  mixed  $value
     */
    public function with($key, $value = null): FieldTypeInterface;
}
