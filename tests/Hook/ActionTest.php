<?php

use Themosis\Hook\ActionBuilder;

class ActionTest extends PHPUnit_Framework_TestCase
{
    /**
     * The service container.
     *
     * @var \Themosis\Foundation\Application
     */
    protected $app;

    public function setUp()
    {
        $this->app = new \Themosis\Foundation\Application();
    }

    public function testActionWithClosure()
    {
        $action = new ActionBuilder($this->app);

        $action->add('init_test', function () {});

        // Check if this action is registered.
        $this->assertTrue($action->exists('init_test'));

        // Check the attached callback is a Closure.
        $this->assertInstanceOf('\Closure', $action->getCallback('init_test')[0]);

        // Check default priority.
        $this->assertEquals(10, $action->getCallback('init_test')[1]);

        // Check default accepted_args.
        $this->assertEquals(3, $action->getCallback('init_test')[2]);
    }

    public function testActionWithClass()
    {
        $action = new ActionBuilder($this->app);

        // Run the action
        $action->add('a_custom_action', 'AnActionClassForTest', 5, 4);

        // Check if this action is registered.
        $this->assertTrue($action->exists('a_custom_action'));

        // Check the attached callback is an array with instance of AnActionClassForTest.
        $class = new AnActionClassForTest();
        $this->assertEquals([$class, 'a_custom_action'], $action->getCallback('a_custom_action')[0]);

        // Check defined priority.
        $this->assertEquals(5, $action->getCallback('a_custom_action')[1]);

        // Check defined accepted args.
        $this->assertEquals(4, $action->getCallback('a_custom_action')[2]);

        // Run the action if pre-defined method.
        $action->add('another_hook', 'AnActionClassForTest@customName');

        // Check this action is registered.
        $this->assertTrue($action->exists('another_hook'));
        // Check attached callback is an array with instance of AnActionClassForTest with method customName
        $this->assertEquals([$class, 'customName'], $action->getCallback('another_hook')[0]);
    }

    public function testActionWithNamedCallback()
    {
        $action = new ActionBuilder($this->app);

        $action->add('some_hook', 'actionHookCallback');

        // Check if this action is registered.
        $this->assertTrue($action->exists('some_hook'));

        // Check if callback is callable (function).
        $this->assertTrue(is_callable($action->getCallback('some_hook')[0]));
    }

    public function testActionIsRanWithoutArguments()
    {
        $action = new ActionBuilder($this->app);

        // Run action without arguments.
        $action->run('my-custom-hook');
        $this->assertTrue(1 == did_action('my-custom-hook'));

        // Check action is ran once.
        $this->assertEquals(1, did_action('my-custom-hook'));

        // Run action a second time...
        $action->run('my-custom-hook');

        // Check action is ran twice.
        $this->assertEquals(2, did_action('my-custom-hook'));
    }

    public function testActionIsRanWithMultipleArguments()
    {
        $action = new ActionBuilder($this->app);

        // Run action with multiple arguments.
        $action->run('some-hook', ['value1', 'value2', 'value3']);

        // Check if this action has run once.
        $this->assertEquals(1, did_action('some-hook'));

        // Run action a second time...
        $action->run('some-hook', ['value4', 'value5']);

        $this->assertEquals(2, did_action('some-hook'));
    }
}
