<?php

use PHPUnit\Framework\TestCase;

class ThemeManagerTest extends TestCase
{
    public function testManagerCanLoadActiveTheme()
    {
        $stub = $this->getMockBuilder('WP_Theme')
            ->setMethods(['get_stylesheet'])
            ->getMock();
        $stub->method('get_stylesheet')
            ->willReturn('sample-theme');

        $this->assertSame('sample-theme', $stub->get_stylesheet());
    }
}
