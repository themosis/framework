<?php

namespace Themosis\User;

use Themosis\Support\Contracts\SectionInterface;
use Themosis\User\Contacts\UserFieldContract;

class UserField implements UserFieldContract
{
    public function add($field, SectionInterface $section = null): UserFieldContract
    {
        // TODO: Implement add() method.
    }
}
