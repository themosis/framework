<?php

namespace Themosis\Support\Facades;

use Illuminate\Support\Facades\Facade;

class UserField extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'themosis.user.field';
    }
}
