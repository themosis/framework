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
     * Reserved options keys.
     *
     * @var array
     */
    protected $reservedOptions = [
        'name',
        'attributes'
    ];

    /**
     * Field name prefix.
     * Applied automatically to avoid conflicts with core query variables.
     *
     * @var string
     */
    protected $prefix = 'th_';

    /**
     * BaseType constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct();
        $this->options['name'] = $this->prefixName($name);
    }

    /**
     * Prefix the field name property.
     *
     * @param string $name
     *
     * @return string
     */
    protected function prefixName(string $name): string
    {
        return trim($this->prefix).$name;
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
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    /**
     * Return field options.
     *
     * @param string $optionKey Optional. Retrieve all options by default or the value based on given option key.
     *
     * @return array|mixed
     */
    public function getOptions(string $optionKey = ''): array
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
