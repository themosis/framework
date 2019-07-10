<?php

namespace Themosis\Support\Facades;

use Illuminate\Support\Facades\Facade;
use Themosis\Taxonomy\Contracts\TaxonomyInterface;
use Themosis\Taxonomy\TaxonomyFieldFactory;

/**
 * @method static \Themosis\Taxonomy\TaxonomyField make(TaxonomyInterface $taxonomy, array $options = [])
 *
 * @see TaxonomyFieldFactory
 */
class TaxonomyField extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'taxonomy.field';
    }
}
