<?php
namespace Themosis\Facades;

abstract class Facade {

    /**
     * The instances to call.
     *
     * @var array
     */
    protected static $instances = array();

    /**
     * Retrieve the class instance used by the facade.
     *
     * @return mixed A class instance.
     */
    private static function getInstance()
    {
        /**
         * Grab the property defined in the child class.
         */
        list($classSlug, $class) = static::$instance;

        /**
         * Check at runtime if an instance already exists.
         * If so, use it and do not create another one.
         */
        if(isset(static::$instances[$classSlug])){
            return static::$instances[$classSlug];
        }

        return static::$instances[$classSlug] = new $class(); // What if the instance has parameters in its constructor - dependencies ?!?
    }

    /**
     * Magic method. Use to dynamically call the registered
     * instance method.
     *
     * @param string $method The class method used.
     * @param array $args The method arguments.
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::getInstance();

        /**
         * Call the instance and its method.
         */
        call_user_func_array(array($instance, $method), $args);
    }

} 