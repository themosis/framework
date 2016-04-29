<?php

use Themosis\Asset\AssetFinder;
use Themosis\Asset\AssetFactory;
use Themosis\Foundation\Application;

class AssetTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var AssetFactory
     */
    protected $factory;

    /**
     * @var \Themosis\Foundation\Application
     */
    protected $container;

    public function setUp()
    {
        $finder = new AssetFinder();
        $finder->addPaths([
            plugins_url('themosis-framework/tests/_assets') => themosis_path('core').'tests/_assets',
        ]);
        $this->container = $container = new Application();
        $this->factory = new AssetFactory($finder, $container);
    }

    public function testTypeDetection()
    {
        $asset = $this->factory->add('project-css', 'css/project-test.css', false, '1.0.0', 'screen');

        // Check we're dealing with the right instance.
        $this->assertInstanceOf('Themosis\Asset\Asset', $asset);
        $this->assertEquals('style', $asset->getType());
        $this->assertEquals('screen', $asset->getArgs('mixed'));
        // Check css asset is loaded on front-end.
        $this->assertEquals('front', $asset->getArea());

        $asset = $this->factory->add('project-script', 'js/project-main.js', ['jquery'], '1.0.0');

        // Check if script asset.
        $this->assertEquals('script', $asset->getType());
        $this->assertFalse($asset->getArgs('mixed'));
        $this->assertEquals('front', $asset->getArea());

        $asset = $this->factory->add('alpine-rescue', 'js/alpine-rescue.min.js', ['jquery'], '1.0', true);

        // Check if script asset.
        $this->assertEquals('script', $asset->getType());
        // Check version.
        $this->assertEquals('1.0', $asset->getArgs('version'));
        // Check is defined to be loaded in the footer.
        $this->assertTrue($asset->getArgs('mixed'));

        // External asset force to a style asset.
        $asset = $this->factory->add('open-sans', 'http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all', false, '1.0', 'all', 'style');

        $this->assertEquals('style', $asset->getType());
        $this->assertEquals('front', $asset->getArea());
        $this->assertEquals('all', $asset->getArgs('mixed')); // Assume it is definitely a style asset, so is loaded in the <head> tag.

        // External asset as script.
        $asset = $this->factory->add('typekit', 'https://use.typekit.net/typekit-id.js', false, null);

        // Check asset is defined as script.
        $this->assertEquals('script', $asset->getType());
        // Check asset is defined to be output in the <head> tag.
        $this->assertFalse($asset->getArgs('mixed'));
    }

    public function testAssetAreAddedToTheContainer()
    {
        $this->factory->add('some-css', 'css/project-test.css');

        $this->assertTrue($this->container->hasShared('asset.some-css'));

        $this->factory->add('some-js', 'js/project-main.js');

        $this->assertTrue($this->container->hasShared('asset.some-js'));
    }

    /**
     * Note: the following tests do not check if the file exists as
     * the asset class assume it is correctly defined in its path argument.
     */
    public function testGetAssetArguments()
    {
        $args = [
            'handle' => 'css-something',
            'path' => 'a/path/to/file.css',
            'deps' => false,
            'version' => '1.2.3',
            'mixed' => 'screen',
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
            'handle' => 'css-handle',
            'path' => 'some/path/to/custom.css',
            'deps' => false,
            'version' => null,
            'mixed' => 'all',
        ]);

        // Check version is null.
        $this->assertNull($asset->getArgs('version'));

        $asset = new \Themosis\Asset\Asset('style', [
            'handle' => 'css-handle',
            'path' => 'some/path/to/custom.css',
            'deps' => false,
            'version' => '',
            'mixed' => 'all',
        ]);

        // Check version is null.
        $this->assertNull($asset->getArgs('version'));

        $asset = new \Themosis\Asset\Asset('style', [
            'handle' => 'css-handle',
            'path' => 'some/path/to/custom.css',
            'deps' => false,
            'version' => false,
            'mixed' => 'all',
        ]);

        // Check version is false.
        $this->assertFalse($asset->getArgs('version'));
    }
}
