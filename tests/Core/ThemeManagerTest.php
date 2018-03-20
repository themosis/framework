<?php

use PHPUnit\Framework\TestCase;
use Themosis\Core\Application;
use Themosis\Core\ThemeManager;

class ThemeManagerTest extends TestCase
{
    public function testManagerCanLoadActiveThemeRoutesFile()
    {
        $stub = $this->getMockBuilder('WP_Theme')
            ->setMethods(['get_stylesheet'])
            ->getMock();

        $app = Application::getInstance();
        $manager = new ThemeManager(
            $app,
            $app->themesPath('sample-theme'),
            $app['action'],
            $stub
        );

        $stub->expects($this->once())
            ->method('get_stylesheet')
            ->will($this->returnValue('sample-theme'));

        $manager->load('resources/routes.php');
        $this->assertSame(
            $app->themesPath('sample-theme/resources/routes.php'),
            $manager->getThemeRoutesPath(),
            'Theme routes file cannot be found.'
        );
    }
}
