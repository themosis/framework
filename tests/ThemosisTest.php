<?php


class ThemosisTest extends WP_UnitTestCase
{
    public function testThemosisSetPathsAndThemosisPaths()
    {
        // Checking the $GLOBALS.
        $this->assertEquals(dirname(__DIR__).'/', $GLOBALS['themosis.paths']['core']);
        $this->assertEquals(dirname(__DIR__).'/src/Themosis/', $GLOBALS['themosis.paths']['sys']);

        // Checking using the function.
        $this->assertEquals(dirname(__DIR__).'/', themosis_path('core'));
        $this->assertEquals(dirname(__DIR__).'/src/Themosis/', themosis_path('sys'));
    }
}
