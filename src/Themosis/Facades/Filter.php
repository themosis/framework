<?php
namespace Themosis\Facades;

class Filter extends Facade
{
    /**
     * Return the igniter service key responsible for the action api classes.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'filter';
    }
}
