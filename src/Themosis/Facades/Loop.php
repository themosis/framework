<?php
namespace Themosis\Facades;

class Loop extends Facade {

    /**
     * Return the facade key used by the view igniter service
     * responsible to load the loop class.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'loop';
    }

}