<?php

include 'Custom.php';

class FacadesTest extends PHPUnit_Framework_TestCase
{
    public function testRunFacades()
    {
        $app = new \Themosis\Foundation\Application();
        $facade = \Themosis\Facades\Facade::setFacadeApplication($app);
        
        // Register a class in the container with the `custom` alias.
        $app->add('custom', new stdClass());

        // Check the `Custom` facade instance is returning a stdClass instance.
        $this->assertInstanceOf('stdClass', Custom::getInstance());
    }
}
