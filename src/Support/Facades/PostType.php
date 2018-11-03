<?php

namespace Themosis\Support\Facades;

use Illuminate\Support\Facades\Facade;

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
