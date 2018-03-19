<?php

namespace Themosis\Facades;

class Input extends Facade
{
    /**
     * Get an item from the input data.
     * This method is used for all request verbs (GET, POST, PUT, and DELETE).
     *
     * @param string $key
     * @param mixed  $default A default value if not found.
     *
     * @return mixed
     */
    public static function get($key = null, $default = null)
    {
        return static::$app['request']->input($key, $default);
    }

    /**
     * Get all of the input and files for the request.
     *
     * @return array
     */
    public static function all()
    {
        return array_merge_recursive(static::$app['request']->input(), static::$app['request']->files->all());
    }

    /**
     * Return the service provider key responsible for the request class.
     * The key must be the same as the one used when registering
     * the service provider.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'request';
    }
}
