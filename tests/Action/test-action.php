<?php

use \Mockery as m;

class ActionTest extends \WP_UnitTestCase
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
        $this->assertEquals(1, did_action('init'));
        $action->run();
    }

    public function testDispatchActionOnCustomHook()
    {
        $i = m::mock('SuperClass');
        $i->shouldReceive(array('anotherMethod' => true))->once();

        $action = \Themosis\Action\Action::listen('custom', $i, 'anotherMethod');
        do_action('custom');
        $this->assertEquals(1, did_action('custom'));
        $this->assertTrue($action->run());
    }
}