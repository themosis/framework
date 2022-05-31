<?php

namespace Themosis\Tests\Core;

use PHPUnit\Framework\TestCase;
use Themosis\Core\HooksRepository;
use Themosis\Hook\Hookable;

class HooksRepositoryTest extends TestCase
{
    public function testHookablesClassesAreRegistered()
    {
        $app = $this->getMockBuilder(\Themosis\Core\Application::class)
            ->disableOriginalConstructor()
            ->getMock();

        $app->expects($this->exactly(2))
            ->method('registerHook');

        (new HooksRepository($app))->load([
            'Some\Namespace\Hookable',
            \Themosis\Tests\Core\MyActions::class,
        ]);
    }
}

class MyActions extends Hookable
{
    public function register()
    {
    }
}
