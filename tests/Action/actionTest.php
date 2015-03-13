<?php

use \Mockery as m;

class ActionTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testListenToActionHook()
    {
        $instance = m::mock('MyClass');

        $action = \Themosis\Action\Action::listen('init', $instance, 'someMethod');
        $this->assertTrue(is_a($action, '\Themosis\Action\Action'));
    }

    public function testDispatchActionOnCoreHook()
    {
        $instance = m::mock('AnotherClass');
        $instance->shouldReceive('myCustomMethod')->once();

        $action = \Themosis\Action\Action::listen('init', $instance, 'myCustomMethod');
        $action->run();
    }

    public function testDispatchActionOnCustomHook()
    {
        $i = m::mock('SuperClass');
        $i->shouldReceive(array('anotherMethod' => true))->once();

        $action = \Themosis\Action\Action::listen('custom', $i, 'anotherMethod');
        $this->assertTrue($action->run());
    }
}