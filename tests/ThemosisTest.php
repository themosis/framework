<?php


class ThemosisTest extends PHPUnit_Framework_TestCase
{
    public function testThemosisSetPathsAndThemosisPaths()
    {
        $paths = [
            'core' => 'some/path/to/core/directory',
            'sys' => 'a/path/to/sys/folder',
        ];

        themosis_set_paths($paths);

        // Checking the $GLOBALS.
        $this->assertEquals('some/path/to/core/directory', $GLOBALS['themosis.paths']['core']);
        $this->assertEquals('a/path/to/sys/folder', $GLOBALS['themosis.paths']['sys']);

        // Checking using the function.
        $this->assertEquals('some/path/to/core/directory', themosis_path('core'));
        $this->assertEquals('a/path/to/sys/folder', themosis_path('sys'));
    }
}
