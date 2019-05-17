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
     * Return field options.
     *
     * @param array $excludes
     *
     * @return array
     */
    public function getOptions(array $excludes = null): array;

    /**
     * Get field type option defined by key.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return string|array|null
     */
    public function getOption(string $key, $default = null);

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
     * Add an attribute to the field.
     *
     * @param string $name
     * @param string $value
     * @param bool   $overwrite By default, it appends the value. Set to true, to replace the existing attribute value.
     *
     * @return FieldTypeInterface
     */
    public function addAttribute(string $name, string $value, $overwrite = false): FieldTypeInterface;

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
     * Set the field view factory instance.
     *
     * @param ViewFactoryInterface $factory
     *
     * @return FieldTypeInterface
     */
    public function setViewFactory(ViewFactoryInterface $factory): FieldTypeInterface;

    /**
     * Return the view instance used by the entity.
     *
     * @param bool $prefixed
     *
     * @return string
     */
    public function getView(bool $prefixed = true): string;

    /**
     * Indicates if the entity has been rendered or not.
     *
     * @return bool
     */
    public function isRendered(): bool;

    /**
     * Check if submitted form is valid.
     *
     * @return bool
     */
    public function isValid(): bool;

    /**
     * Check if submitted form is not valid.
     *
     * @return bool
     */
    public function isNotValid(): bool;

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
     * @param mixed $default
     *
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

    /**
     * Return the field locale.
     *
     * @return string
     */
    public function getLocale(): string;

    /**
     * Set the field locale.
     *
     * @param string $locale
     *
     * @return FieldTypeInterface
     */
    public function setLocale(string $locale): FieldTypeInterface;

    /**
     * Return the field theme.
     *
     * @return string
     */
    public function getTheme(): string;

    /**
     * Set the field theme.
     *
     * @param string $theme
     *
     * @return FieldTypeInterface
     */
    public function setTheme(string $theme): FieldTypeInterface;

    /**
     * Return the Fractal manager.
     *
     * @return Manager
     */
    public function getManager(): Manager;

    /**
     * Set the Fractal manager.
     *
     * @param Manager $manager
     *
     * @return FieldTypeInterface
     */
    public function setManager(Manager $manager): FieldTypeInterface;

    /**
     * Return the transformer factory.
     *
     * @return Factory
     */
    public function getResourceTransformerFactory(): Factory;

    /**
     * Set the transformer factory.
     *
     * @param Factory $factory
     *
     * @return FieldTypeInterface
     */
    public function setResourceTransformerFactory(Factory $factory): FieldTypeInterface;

    /**
     * Return the resource transformer.
     *
     * @return string
     */
    public function getResourceTransformer(): string;

    /**
     * Return a JSON representation of the field.
     *
     * @return string
     */
    public function toJson(): string;

    /**
     * Return an associative array representation of the field.
     *
     * @return array
     */
    public function toArray(): array;

    /**
     * Return the field type.
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Return the field component name.
     *
     * @return string
     */
    public function getComponent(): string;

    /**
     * Pass custom data to the field view.
     *
     * @param string|array $key
     * @param mixed        $value
     *
     * @return FieldTypeInterface
     */
    public function with($key, $value = null): FieldTypeInterface;
}
