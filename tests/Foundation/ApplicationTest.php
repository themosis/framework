<?php

use Themosis\Foundation\Application;

include 'AppNoDependencies.php';

class ApplicationTest extends PHPUnit_Framework_TestCase
{
    public function testApplicationIsInTheContainer()
    {
        $app = new Application();
        $app = $app['app'];

        $this->assertTrue($app instanceof Themosis\Foundation\Application);
    }

    public function testPathsAreRegisteredInTheContainer()
    {
        $app = new Application();
        $app->registerAllPaths(themosis_path());

        $this->assertEquals($app['path.core'], themosis_path('core'));
    }

    public function testAutoWireInstanceWithoutDependencies()
    {
        $app = new Application();

        $this->assertInstanceOf('AppNoDependencies', $app->make('AppNoDependencies'));
    }
}
