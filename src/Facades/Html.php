<?php

namespace Themosis\Facades;

class Html extends Facade
{
    /**
     * Return the service provider key responsible for the html class.
     * The key must be the same as the one used when registering
     * the service provider.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'html';
    }
}
