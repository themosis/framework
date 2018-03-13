<?php

use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
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
}
