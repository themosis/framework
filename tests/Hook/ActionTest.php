<?php
require_once __DIR__ . '/helpers/AnActionClassForTestHelper.php';

use PHPUnit\Framework\TestCase;
use Themosis\Core\Application;
use Themosis\Hook\ActionBuilder;

class ActionTest extends TestCase
{
    /**
     * @var Application
     */
    protected $app;

    public function setUp(): void
    {
        $this->app = new Application();
    }

    public function testActionWithClosure()
    {
        $action = $this->getMockBuilder(ActionBuilder::class)
            ->setConstructorArgs([$this->app])
            ->onlyMethods(['addAction'])
            ->getMock();

        $action->expects($this->once())
            ->method('addAction');

        $action->add('init_test', function () {
        });

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
        $action = $this->getMockBuilder(ActionBuilder::class)
            ->setConstructorArgs([$this->app])
            ->onlyMethods(['addAction'])
            ->getMock();

        $action->expects($this->exactly(2))
            ->method('addAction');

        // Run the action
        $action->add('a_custom_action', 'AnActionClassForTestHelper', 5, 4);

        // Check if this action is registered.
        $this->assertTrue($action->exists('a_custom_action'));

        // Check the attached callback is an array with instance of AnActionClassForTestHelper.
        $class = new AnActionClassForTestHelper();
        $this->assertEquals([$class, 'a_custom_action'], $action->getCallback('a_custom_action')[0]);

        // Check defined priority.
        $this->assertEquals(5, $action->getCallback('a_custom_action')[1]);

        // Check defined accepted args.
        $this->assertEquals(4, $action->getCallback('a_custom_action')[2]);

        // Run the action if pre-defined method.
        $action->add('another_hook', 'AnActionClassForTestHelper@customName');

        // Check this action is registered.
        $this->assertTrue($action->exists('another_hook'));
        // Check attached callback is an array with instance of AnActionClassForTestHelper with method customName
        $this->assertEquals([$class, 'customName'], $action->getCallback('another_hook')[0]);
    }

    public function testActionWithNamedCallback()
    {
        $action = $this->getMockBuilder(ActionBuilder::class)
            ->setConstructorArgs([$this->app])
            ->onlyMethods(['addAction'])
            ->getMock();

        $action->expects($this->once())->method('addAction');

        $action->add('some_hook', 'actionHookCallback');

        // Check if this action is registered.
        $this->assertTrue($action->exists('some_hook'));

        // Check if callback is callable (function).
        $this->assertTrue(is_callable($action->getCallback('some_hook')[0]));
    }

    public function testActionUsingCurrentInstance()
    {
        $action = $this->getMockBuilder(ActionBuilder::class)
            ->setConstructorArgs([$this->app])
            ->onlyMethods(['addAction'])
            ->getMock();

        $action->expects($this->once())->method('addAction');

        $action->add('after-custom-setup', [$this, 'afterSetup']);

        // Check if this action is registered.
        $this->assertTrue($action->exists('after-custom-setup'));

        // Check if callback is this instance.
        $this->assertEquals([$this, 'afterSetup'], $action->getCallback('after-custom-setup')[0]);
        $this->assertInstanceOf('ActionTest', $action->getCallback('after-custom-setup')[0][0]);
    }

    public function testActionIsRanWithoutArguments()
    {
        $action = $this->getMockBuilder(ActionBuilder::class)
            ->setConstructorArgs([$this->app])
            ->onlyMethods(['doAction'])
            ->getMock();

        $action->expects($this->exactly(2))
            ->method('doAction');

        // Run action without arguments.
        $action->run('my-custom-hook');
        // Run action a second time...
        $action->run('my-custom-hook');
    }

    public function testActionIsRanWithMultipleArguments()
    {
        $action = $this->getMockBuilder(ActionBuilder::class)
            ->setConstructorArgs([$this->app])
            ->onlyMethods(['doActionRefArray'])
            ->getMock();

        $action->expects($this->exactly(2))->method('doActionRefArray');

        // Run action with multiple arguments.
        $action->run('some-hook', ['value1', 'value2', 'value3']);
        // Run action a second time...
        $action->run('some-hook', ['value4', 'value5']);
    }

    public function testCanListenToMultipleActionsAtOnce()
    {
        $action = $this->getMockBuilder(ActionBuilder::class)
            ->setConstructorArgs([$this->app])
            ->onlyMethods(['addAction'])
            ->getMock();

        $action->expects($this->exactly(3))->method('addAction');

        $action->add(['init', 'admin-init', 'user-init'], [$this, 'someMethod']);

        $this->assertTrue($action->exists('init'));
        $this->assertTrue($action->exists('admin-init'));
        $this->assertTrue($action->exists('user-init'));
        $this->assertEquals([$this, 'someMethod'], $action->getCallback('init')[0]);
        $this->assertEquals([$this, 'someMethod'], $action->getCallback('admin-init')[0]);
        $this->assertEquals([$this, 'someMethod'], $action->getCallback('user-init')[0]);
    }
}
