<?php

use Themosis\Foundation\Application;

class ApplicationTest extends PHPUnit_Framework_TestCase
{
    public function testApplicationIsInTheContainer()
    {
        $app = new Application();
        $app = $app->get('app');

        $this->assertTrue($app instanceof Themosis\Foundation\Application);
    }

    public function testPathsAreRegisteredInTheContainer()
    {
        $app = new Application();
        $app->registerAllPaths(themosis_path());

        $this->assertEquals($app->get('path.core'), themosis_path('core'));
    }

    public function testAddAndGetInstancesUsingArrayAccessMethods()
    {
        $app = new Application();

        $app['myclass'] = new stdClass();

        $this->assertTrue($app->has('myclass'));
        $this->assertInstanceOf('StdClass', $app['myclass']);

        // Isset
        $this->assertTrue(isset($app['myclass']));

        // Unset
        unset($app['myclass']);
        $this->assertFalse($app->has('myclass'));
    }
}
