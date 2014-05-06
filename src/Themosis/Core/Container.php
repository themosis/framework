<?php
namespace Themosis\Core;

use ArrayAccess;

abstract class Container implements ArrayAccess{

    /**
     * Available igniters to the application.
     *
     * @var array
     */
    protected $igniters = array();

    /**
     * Loaded instances aka the builders. (FormBuilder,...)
     *
     * @var array
     */
    protected $instances = array();

    /**
     * An offset to check for. Run when used with isset()
     *
     * @link http://www.php.net/manual/fr/class.arrayaccess.php
     * @param string $key The key name of the igniter.
     * @return bool True on success or false on failure.
     */
    public function offsetExists($key)
    {
        return isset($this->instances[$key]);
    }

    /**
     * Instance to retrieve. Run when using $obj[$key]
     *
     * @link http://www.php.net/manual/fr/class.arrayaccess.php
     * @param string $key The key name of the igniter.
     * @return mixed
     */
    public function offsetGet($key)
    {
        return isset($this->instances[$key]) ? $this->instances[$key] : null;
    }

    /**
     * Add an instance to the list. Run when using $obj[$key] = $value
     *
     * @link http://www.php.net/manual/fr/class.arrayaccess.php
     * @param string $key The instance key name.
     * @param mixed $value The instance to add.
     * @return void
     */
    public function offsetSet($key, $value)
    {
        if (is_null($key)) {
            $this->instances[] = $value;
        } else {
            $this->instances[$key] = $value;
        }
    }

    /**
     * Remove an instance of the list. Run when used by unset($obj[$key])
     *
     * @link http://www.php.net/manual/fr/class.arrayaccess.php
     * @param string $key The instance key name.
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->instances[$key]);
    }
}