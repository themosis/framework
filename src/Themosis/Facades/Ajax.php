<?php
namespace Themosis\Facades;

class Ajax extends Facade
{
    /**
     * Return the igniterService key responsible for the ajax api.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ajax';
    }
}