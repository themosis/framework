<?php

namespace Themosis\Support\Facades;

use Illuminate\Support\Facades\Facade;

class Ajax extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ajax';
    }
}
