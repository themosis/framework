<?php

namespace Themosis\Core\Dropins;

class WordPressDropins
{
    /**
     * WordPress drop-ins files.
     *
     * @var array
     */
    public static $dropins = [
        'advanced-cache' => 'advanced-cache.php',
        'blog-deleted' => 'blog-deleted.php',
        'blog-inactive' => 'blog-inactive.php',
        'blog-suspended' => 'blog-suspended.php',
        'db' => 'db.php',
        'db-error' => 'db-error.php',
        'install' => 'install.php',
        'maintenance' => 'maintenance.php',
        'object-cache' => 'object-cache.php',
        'sunrise' => 'sunrise.php',
    ];

    /**
     * Return a list of publishable drop-in files.
     *
     * @return array
     */
    public static function publishableDropins()
    {
        return static::$dropins;
    }

    /**
     * Return a list of drop-in files paths.
     *
     * @param  string|null  $key
     * @return array
     */
    public static function dropinPaths($key = null)
    {
        if (is_null($key)) {
            return array_values(static::$dropins);
        }

        if (isset(static::$dropins[$key])) {
            return [static::$dropins[$key]];
        }

        return [];
    }
}
