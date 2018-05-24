<?php

namespace Themosis\Forms\Fields\Types;

use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Html\HtmlBuilder;

abstract class BaseType extends HtmlBuilder implements \ArrayAccess, \Countable, FieldTypeInterface
{
    /**
     * List of options.
     *
     * @var array
     */
    protected $options;

    /**
     * Allowed options keys.
     *
     * @var array
     */
    protected $allowedOptions = [
        'group'
    ];

    /**
     * List of default options per field.
     *
     * @var array
     */
    protected $defaultOptions = [
        'group' => 'default'
    ];

    /**
     * Field name prefix.
     * Applied automatically to avoid conflicts with core query variables.
     *
     * @var string
     */
    protected $prefix = 'th_';

    /**
     * The field basename.
     * Name property without the prefix as defined by the user.
     *
     * @var string
     */
    protected $baseName;

    /**
     * BaseType constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct();
        $this->baseName = $name;
        $this->prefixName($name);
    }

    /**
     * Return the list of default options.
     *
     * @return array
     */
    public function getDefaultOptions(): array
    {
        return $this->defaultOptions;
    }

    /**
     * Return allowed options for the field.
     *
     * @return array
     */
    public function getAllowedOptions(): array
    {
        return $this->allowedOptions;
    }

    /**
     * Prefix the field name property.
     *
     * @param string $name The name property value (base name).
     *
     * @return $this
     */
    protected function prefixName(string $name): FieldTypeInterface
    {
        $this->options['name'] = trim($this->prefix).$name;

        return $this;
    }

    /**
     * Set field options.
     *
     * @param array $options
     *
     * @return FieldTypeInterface
     */
    public function setOptions(array $options): FieldTypeInterface
    {
        // A user cannot override the "name" property.
        if (isset($options['name'])) {
            throw new \InvalidArgumentException('The "name" option can not be overridden.');
        }

        $this->options = array_merge($this->options, $options);

        return $this;
    }

    /**
     * Return field options.
     *
     * @param string $optionKey Optional. Retrieve all options by default or the value based on given option key.
     *
     * @return mixed
     */
    public function getOptions(string $optionKey = '')
    {
        return $this->options[$optionKey] ?? $this->options;
    }

    /**
     * Set the field prefix.
     *
     * @param string $prefix
     *
     * @return FieldTypeInterface
     */
    public function setPrefix(string $prefix): FieldTypeInterface
    {
        $this->prefix = $prefix;

        // Automatically update the "name" option based
        // on the new prefix.
        $this->prefixName($this->getBaseName());

        return $this;
    }

    /**
     * Return the field prefix.
     *
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * Return the field name property value.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->getOptions('name');
    }

    /**
     * Return the field basename.
     *
     * @return string
     */
    public function getBaseName(): string
    {
        return $this->baseName;
    }

    /**
     * Return the field attributes.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->getOptions('attributes');
    }

    /**
     * Set the field attributes.
     *
     * @param array $attributes
     *
     * @return FieldTypeInterface
     */
    public function setAttributes(array $attributes)
    {
        $this->options['attributes'] = $attributes;

        return $this;
    }

    /**
     * Whether a offset exists.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset An offset to check for.
     *
     * @return bool true on success or false on failure.
     *
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->options[$offset]);
    }

    /**
     * Offset to retrieve
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset The offset to retrieve.
     *
     * @return mixed Can return all value types.
     *
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return isset($this->options[$offset]) ? $this->options[$offset] : null;
    }

    /**
     * Offset to set
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset The offset to assign the value to.
     * @param mixed $value  The value to set.
     *
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->options[] = $value;
        } else {
            $this->options[$offset] = $value;
        }
    }

    /**
     * Offset to unset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset The offset to unset.
     *
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        unset($this->options[$offset]);
    }

    /**
     * Count elements of an object
     *
     * @link http://php.net/manual/en/countable.count.php
     *
     * @return int The custom count as an integer.
     *
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->options);
    }
}
