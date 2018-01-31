<?php


class ThemosisTest extends PHPUnit_Framework_TestCase
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

    /**
     * Test themosis_path() function is returning all registered
     * paths if no parameter is given.
     */
    public function testThemosisGetAllPaths()
    {
        $this->assertTrue(count($GLOBALS['themosis.paths']) === count(themosis_path()));
    }
}
