<?php
namespace Themosis\Facades;

class User extends Facade {

    /**
     * Each facade must define their igniter service
     * class key name.
     *
     * @throws \RuntimeException
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'user';
    }

} 