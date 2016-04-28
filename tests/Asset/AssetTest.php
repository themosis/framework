<?php

class AssetTest extends PHPUnit_Framework_TestCase
{
    public function testGetAssetArguments()
    {
        $args = [
            'handle'    => 'css-something',
            'path'      => 'a/path/to/file.css',
            'deps'      => false,
            'version'   => '1.2.3',
            'mixed'     => 'screen'
        ];
        $asset = new \Themosis\Asset\Asset('style', $args);

        // Check get all arguments back.
        $this->assertEquals($args, $asset->getArgs());

        // Check get handle property.
        $this->assertEquals('css-something', $asset->getArgs('handle'));

        // Check get path property.
        $this->assertEquals('a/path/to/file.css', $asset->getArgs('path'));

        // Check get deps property.
        $this->assertEquals(false, $asset->getArgs('deps'));

        // Check get version property.
        $this->assertEquals('1.2.3', $asset->getArgs('version'));

        // Check get mixed property.
        $this->assertEquals('screen', $asset->getArgs('mixed'));
    }

    public function testAssetVersion()
    {
        $asset = new \Themosis\Asset\Asset('style', [
            'handle'    => 'css-handle',
            'path'      => 'some/path/to/custom.css',
            'deps'      => false,
            'version'   => null,
            'mixed'     => 'all'
        ]);

        // Check version is null.
        $this->assertNull($asset->getArgs('version'));

        $asset = new \Themosis\Asset\Asset('style', [
            'handle'    => 'css-handle',
            'path'      => 'some/path/to/custom.css',
            'deps'      => false,
            'version'   => '',
            'mixed'     => 'all'
        ]);

        // Check version is null.
        $this->assertNull($asset->getArgs('version'));

        $asset = new \Themosis\Asset\Asset('style', [
            'handle'    => 'css-handle',
            'path'      => 'some/path/to/custom.css',
            'deps'      => false,
            'version'   => false,
            'mixed'     => 'all'
        ]);

        // Check version is false.
        $this->assertFalse($asset->getArgs('version'));
    }
}