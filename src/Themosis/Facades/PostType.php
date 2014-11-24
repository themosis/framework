<?php
namespace Themosis\Facades;

class PostType extends Facade {

    /**
     * Return the igniter service key responsible for the PostType class.
     * The key must be the same as the one used in the assigned
     * igniter service.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'posttype';
    }


} 