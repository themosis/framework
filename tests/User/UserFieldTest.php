<?php

namespace Themosis\Tests\User;

use PHPUnit\Framework\TestCase;
use Themosis\Tests\Application;
use Themosis\Tests\ViewFactory;
use Themosis\User\UserField;

class UserFieldTest extends TestCase
{
    use Application, ViewFactory;

    public function testUserAddField()
    {
        $user = new UserField();
    }
}
