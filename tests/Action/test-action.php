<?php

use \Mockery as m;

class ActionTest extends WP_UnitTestCase
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
}