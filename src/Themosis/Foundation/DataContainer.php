<?php

namespace Themosis\Foundation;

use ArrayAccess;

abstract class DataContainer implements ArrayAccess
{
    /**
     * Instance properties.
     *
     * @var array
     */
    protected $properties = [];

    /**
     * Check if a property exists.
     *
     * @param string $offset The property key.
     *
     * @return bool True on success, false on failure.
     */
    public function offsetExists($offset)
    {
        return isset($this->properties[$offset]);
    }

    /**
     * Property to fetch.
     *
     * @param string $offset The property key.
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->properties[$offset] : null;
    }

    /**
     * Set a new property.
     *
     * @param string $offset The property key.
     * @param mixed  $value  The property value.
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->properties[] = $value;
        } else {
            $this->properties[$offset] = $value;
        }
    }

    /**
     * Property to remove.
     *
     * @param string $offset The property key.
     */
    public function offsetUnset($offset)
    {
        unset($this->properties[$offset]);
    }
}
