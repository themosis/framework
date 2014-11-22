<?php
namespace Themosis\Facades;

abstract class Facade {

    /**
     * The Application instance.
     *
     * @var \Themosis\Core\Application
     */
    protected static $app;

    /**
     * The resolved object instances.
     *
     * @var array
     */
    protected static $resolvedInstances;

    /**
     * Each facade must define their igniter service
     * class key name.
     *
     * @throws \RuntimeException
     * @return string
     */
    protected static function getFacadeKey()
    {
        throw new \RuntimeException('Facade does not implement getInstanceKey method.');
    }

    /**
     * Retrieve the instance called by the igniter service.
     *
     * @return mixed
     */
    private static function getInstance()
    {
        /**
         * Grab the igniter service class and get the instance
         * called by the service.
         */
        return static::resolveFacadeInstance(static::getFacadeKey());
    }

    /**
     * Return a facade instance if one already exists. If not, keep a copy
     * of all instances and return the current called one.
     *
     * @param string $name
     * @return mixed
     */
    private static function resolveFacadeInstance($name)
    {
        if (is_object($name)) return $name;

        if (isset(static::$resolvedInstances[$name]))
        {
            return static::$resolvedInstances[$name];
        }

        return static::$resolvedInstances[$name] = static::$app[$name];
    }

    /**
     * Clear a resolved facade instance.
     *
     * @param string $name
     * @return void
     */
    public static function clearResolvedInstance($name)
    {
        unset(static::$resolvedInstances[$name]);
    }

    /**
     * Clear all of the resolved instances.
     *
     * @return void
     */
    public static function clearResolvedInstances()
    {
        static::$resolvedInstances = array();
    }

    /**
     * Store the application instance.
     *
     * @param \Themosis\Core\Application $app
     * @return void
     */
    public static function setFacadeApplication($app)
    {
        static::$app = $app;
    }

    /**
     * Magic method. Use to dynamically call the registered
     * instance method.
     *
     * @param string $method The class method used.
     * @param array $args The method arguments.
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::getInstance();

        /**
         * Call the instance and its method.
         */
        switch (count($args))
        {
            case 0:
                return $instance->$method();
            case 1:
                return $instance->$method($args[0]);
            case 2:
                return $instance->$method($args[0], $args[1]);
            case 3:
                return $instance->$method($args[0], $args[1], $args[2]);
            case 4:
                return $instance->$method($args[0], $args[1], $args[2], $args[3]);
            default:
                return call_user_func_array(array($instance, $method), $args);
        }
        //return call_user_func_array(array($instance, $method), $args);
    }

} 