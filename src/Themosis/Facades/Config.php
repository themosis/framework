<?php

namespace Themosis\Facades;

use ArrayAccess;

class Config extends Facade implements ArrayAccess
{
    /**
     * Return the service provider key responsible for the config class.
     * The key must be the same as the one used when registering
     * the service provider.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'config.factory';
    }

    /**
     * Determines if an offset exists in a config for ArrayAccess
     *
     * @param  mixed  $offset
     *
     * @return  boolean
     */
    public function offsetExists($offset)
    {
      return self::offsetExists($offset);
    }

    /**
     * Returns an offset from the config for ArrayAccess interface
     *
     * @param  mixed  $offset
     *
     * @return  mixed
     */
    public function offsetGet($offset)
    {
        return self::offsetGet($offset);
    }

    /**
     * Implementing for ArrayAccess, doesn't do anything because configs are read
     * only.
     *
     * @param  mixed  $offset
     * @param  mixed  $value
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        self::offsetSet($offset, $value);
    }

    /**
     * Implementing for ArrayAccess, doesn't do anything because configs are read
     * only.
     *
     * @param  mixed  $offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        self::offsetSet($offset);
    }
}
