<?php

namespace Themosis\Config;

use ArrayAccess;

class ConfigFactory implements IConfig, ArrayAccess
{
    /**
     * Config file finder instance.
     *
     * @var ConfigFinder
     */
    protected $finder;

    public function __construct(ConfigFinder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * Return all or specific property from a config file.
     *
     * @param string $name The config file name or its property full name.
     *
     * @return mixed
     */
    public function get($name)
    {
        if (strpos($name, '.') !== false) {
            $parts = explode('.', $name);
            list($name, $property) = $parts;
        }

        $path = $this->finder->find($name);
        $properties = include $path;

        // Looking for a single property
        if (isset($property)) {
          if (array_key_exists($property, $properties)) {
            // Looking for a single property in an array
            if (is_array($properties[$property])) {
              return $this->getRecursive(array_slice($parts, 1), $properties);
            }
            // Return the single property
            else {
              return $properties[$property];
            }
          }
        }
        // Return all properties
        else {
          return $properties;
        }
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
      $value = $this->get($offset);

      if ($value) {
          return true;
      }
      else {
          return false;
      }
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
        return $this->get($offset);
    }

    /**
     * Recursively searches an array for a key
     *
     * @param  array  $keys
     * @param  array  $array
     *
     * @return mixed
     */
    protected function getRecursive(array $keys, array $array)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $array)) {
                $array = $array[$key];
            }
            else {
                return;
            }
        }

        return $array;
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
    public function offsetSet($offset, $value) {}

    /**
     * Implementing for ArrayAccess, doesn't do anything because configs are read
     * only.
     *
     * @param  mixed  $offset
     *
     * @return void
     */
    public function offsetUnset($offset) {}
}
