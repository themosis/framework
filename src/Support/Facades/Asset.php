<?php

namespace Themosis\Support\Facades;

use Illuminate\Support\Facades\Facade;
use Themosis\Asset\AssetInterface;
use Themosis\Asset\Factory;

/**
 * @method static AssetInterface add(string $handle, string $path, array $dependencies = [], $version = null, $arg = null)
 *
 * @see \Themosis\Asset\Factory
 */
class Asset extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Factory::class;
    }
}
