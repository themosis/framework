<?php

class Themosis_Test extends WP_UnitTestCase
{
    /**
     * Check if the main class is stored in the $GLOBALS.
     *
     * @return void
     */
    public function testThemosisFrameworkIsAvailable()
    {
        $this->assertTrue(isset($GLOBALS['THFWK_Themosis']));
    }

    /**
     * Check the instance type of the Themosis framework.
     *
     * @return void
     */
    public function testIsAThemosisFrameworkInstance()
    {
        $instance = $GLOBALS['THFWK_Themosis'];
        $this->assertTrue(is_a($instance, 'THFWK_Themosis'));
    }

    /**
     * Check if core directories exist.
     *
     * @return void
     */
    public function testThemosisFrameworkCoreDirectoryPaths()
    {
        $paths = array('sys', 'datas', 'admin', 'storage');

        foreach($paths as $path)
        {
            $this->assertTrue(file_exists(themosis_path($path)));
        }
    }

    /**
     * Check if the plugin directory name property is not empty.
     *
     * @return void
     */
    public function testThemosisFrameworkPluginDirectoryNamePropertyIsNotEmpty()
    {
        $instance = $GLOBALS['THFWK_Themosis'];
        $directory = $instance::getDirName();

        $this->assertTrue(!empty($directory));
    }
} 