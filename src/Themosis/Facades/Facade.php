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
        return static::$app->fire(static::getFacadeKey());

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
        return call_user_func_array(array($instance, $method), $args);
    }

} 