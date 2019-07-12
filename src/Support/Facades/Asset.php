<?php

namespace Themosis\Support\Facades;

use Illuminate\Support\Facades\Facade;
use Themosis\Asset\AssetInterface;

/**
 * @method static AssetInterface add(string $handle, string $path, array $dependencies = [], $version = null, $arg = null)
 *
 * @see \Themosis\Asset\Factory
 */
class Asset extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'asset';
    }
}
