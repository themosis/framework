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
}