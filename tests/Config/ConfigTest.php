<?php

class ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Themosis\Config\ConfigFinder
     */
    protected $finder;

    /**
     * @var \Themosis\Config\ConfigFactory
     */
    protected $factory;

    public function setUp()
    {
        $this->finder = new \Themosis\Config\ConfigFinder();
        $this->finder->addPaths([
            themosis_path('core').'tests/Config/configFiles/',
        ]);
        $this->factory = new \Themosis\Config\ConfigFactory($this->finder);
    }

    public function testLoadConfigFileWithConfigDotPhpExtension()
    {
        $values = $this->factory->get('project');

        // Check returned values.
        $this->assertTrue(is_array($values));
        $this->assertEquals([
            'key' => 'value',
            'access' => [
                'administrator',
                'editor',
            ],
            'name' => 'themosis',
            'multi-access' => [
                'key' => 'value2',
            ],
        ], $values);

        // Check return single value.
        $name = $this->factory->get('project.name');

        $this->assertEquals('themosis', $name);
    }

    public function testLoadConfigFileWithPhpExtension()
    {
        $values = $this->factory->get('sample');

        // Check returned values.
        $this->assertTrue(is_array($values));
        $this->assertEquals([
            'key1' => 'value1',
            'theme' => 'Themosis Theme',
            'namespace' => 'custom-name',
        ], $values);

        // Check returned single value.
        $theme = $this->factory->get('sample.theme');

        $this->assertEquals('Themosis Theme', $theme);
    }

    public function testGetMultiDimensionConfigValue()
    {
        $this->assertEquals('value2', $this->factory->get('project.multi-access.key'));
    }

    public function testArrayAccessOffsetGet()
    {
        $this->assertEquals('value', $this->factory['project.key']);
        $this->assertEquals('value2', $this->factory['project.multi-access.key']);
    }

    public function testArrayAccessOffsetExists()
    {
        $this->assertTrue(isset($this->factory['project.key']));
        $this->assertFalse(isset($this->factory['project.made-up']));
    }
}
