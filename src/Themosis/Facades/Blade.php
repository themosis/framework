<?php

namespace Themosis\Facades;

class Blade extends Facade
{
    /**
     * Return the service provider key responsible for the Blade compiler.
     * The key must be the same as the one used when registering in
     * the service provider.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'blade';
    }
}
