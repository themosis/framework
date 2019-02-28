<?php

namespace Themosis\Support\Facades;

use Illuminate\Support\Facades\Facade;

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
