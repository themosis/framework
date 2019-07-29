<?php

namespace Themosis\Support\Facades;

use Illuminate\Support\Facades\Facade;
use Themosis\Metabox\Contracts\MetaboxInterface;
use Themosis\Metabox\Factory;

/**
 * @method static MetaboxInterface make(string $id, $screen = 'post')
 *
 * @see Factory
 */
class Metabox extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'metabox';
    }
}
