<?php

namespace Themosis\Support\Facades;

use Illuminate\Support\Facades\Facade;
use Themosis\Ajax\AjaxInterface;

/**
 * @method static AjaxInterface listen($action, $callback, $logged = 'both')
 *
 * @see \Themosis\Ajax\Ajax
 */
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
