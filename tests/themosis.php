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
            $this->assertTrue(file_exists(themosis_path($path)), "Path not found.");
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

    /**
     * Check plugin configuration files.
     *
     * @return void
     */
    public function testLoadThemosisPluginConfigurationFiles()
    {
        $configs = array(
            'datas' => array(
                'application',
                'constants',
                'errors',
                'images'
            )
        );

        $this->assertTrue(\Themosis\Configuration\Config::make($configs), 'A config file is missing or there is an error in its name.');

        // Check saved configurations.
        $reflection = new ReflectionClass('\Themosis\Configuration\Config');
        $allConfigs = $reflection->getStaticProperties();

        foreach ($allConfigs as $value)
        {
            $this->assertTrue(is_array($value), "The configuration is not an array.");

            foreach ($value as $index => $config)
            {
                // Check config file path.
                $this->assertTrue(file_exists($config['path']), "File [{$config['name']}] not found.");
                // Compare plugin config file name.
                if (isset($configs['datas'][$index]))
                {
                    $this->assertEquals($configs['datas'][$index], $config['name']);
                }
            }
        }
    }

    /**
     * Verify value type of each Application configuration.
     *
     * @return void
     */
    public function testApplicationConfiguration()
    {
        $configuration = array(
            'name'  => 'application',
            'path'  => themosis_path('datas').'config'.DS.'application'.CONFIG_EXT
        );

        $application = new \Themosis\Configuration\Application();
        $application->set($configuration['path']);

        $reflection = new ReflectionClass($application);
        $properties = $reflection->getStaticProperties();

        foreach ($properties as $property)
        {
            $this->assertTrue(is_array($property), "The application config file do not return an array.");

            // Check each config value type.
            $this->assertTrue(is_string($property['textdomain']), "Textdomain property should be a string value.");
            $this->assertTrue(is_string($property['namespace']), "Namespace property should be a string value.");
            $this->assertTrue(is_string($property['ajaxurl']), "Ajaxurl property should be a string value.");
            $this->assertTrue(is_string($property['encoding']), "Encoding property should be a string value.");
            $this->assertTrue(is_bool($property['rewrite']), "Rewrite property should be a boolean value.");
            $this->assertTrue(is_string($property['loginurl']), "Loginurl property should be a string value.");
            $this->assertTrue(is_bool($property['cleanup']), "Cleanup property should be a boolean value.");
            $this->assertTrue(is_bool($property['htaccess']), "Htaccess property should be a boolean value.");
            $this->assertTrue(is_array($property['access']), "Access property should be an array value.");
            $this->assertTrue(is_array($property['aliases']), "Aliases property should be an array value.");
        }
    }
} 