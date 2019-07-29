<?php

namespace Themosis\Support\Facades;

use Illuminate\Support\Facades\Facade;
use Themosis\Taxonomy\Contracts\TaxonomyInterface;
use Themosis\Taxonomy\Factory;

/**
 * @method static TaxonomyInterface make(string $slug, $objects, string $plural, string $singular)
 *
 * @see Factory
 */
class Taxonomy extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'taxonomy';
    }
}
