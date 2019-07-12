<?php

namespace Themosis\Support\Facades;

use Illuminate\Support\Facades\Facade;
use Themosis\PostType\Contracts\PostTypeInterface;
use Themosis\PostType\Factory;

/**
 * @method static PostTypeInterface make(string $slug, string $plural, string $singular)
 * @method static bool exists(string $slug)
 * @method static PostTypeInterface|null get(string $slug)
 *
 * @see Factory
 */
class PostType extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'posttype';
    }
}
