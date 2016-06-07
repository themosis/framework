<?php

namespace Themosis\Facades;

class Section extends Facade
{
    /**
     * Return the service provider key responsible for the section class.
     * The key must be the same as the one used when registering
     * the service provider.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'sections';
    }
}
