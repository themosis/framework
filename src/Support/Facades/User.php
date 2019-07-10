<?php

namespace Themosis\Support\Facades;

use Illuminate\Support\Facades\Facade;
use Themosis\User\Factory;

/**
 * @method static \Themosis\User\User make(string $username, string $password, string $email)
 * @method static \Themosis\User\User current()
 * @method static \Themosis\User\User get(int $user_id)
 *
 * @see Factory
 */
class User extends Facade
{
    /**
     * Return the service provider key responsible for the user class.
     * The key must be the same as the one used when registering
     * the service provider.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'themosis.user';
    }
}
