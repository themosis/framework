<?php
namespace Themosis\Facades;

class Page extends Facade {

    /**
     * Return the igniter service key responsible for the Page class.
     * The key must be the same as the one used in the assigned
     * igniter service.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'page';
    }

} 