<?php
namespace Themosis\Facades;

class Metabox extends Facade {

    /**
     * Return the igniter service key responsible for the Metabox class.
     * The key must be the same as the one used in the assigned
     * igniter service.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'metabox';
    }

}

?>