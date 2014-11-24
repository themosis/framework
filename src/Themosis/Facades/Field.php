<?php
namespace Themosis\Facades;

class Field extends Facade {

    /**
     * Return the igniter service key responsible for the form class.
     * The key must be the same as the one used in the assigned
     * igniter service.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'field';
    }

} 