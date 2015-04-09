<?php
namespace Themosis\Facades;

class Action extends Facade
{
    /**
     * Return the igniter service key responsible for the action api classes.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'action';
    }
}