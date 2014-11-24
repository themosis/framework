<?php
namespace Themosis\Facades;

class View extends Facade {

    /**
     * Each facade must define their igniter service
     * class key name.
     *
     * @throws \RuntimeException
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'view';
    }

} 