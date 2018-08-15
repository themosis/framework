<?php

namespace Themosis\Tests\Assets;

use Illuminate\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Themosis\Asset\Asset;
use Themosis\Asset\AssetInterface;
use Themosis\Asset\Factory;
use Themosis\Asset\Finder;

class AssetsTest extends TestCase
{
    public function getFinder()
    {
        $finder = new Finder(new Filesystem());
        $finder->addLocation(
            __DIR__.'/files',
            'https://www.domain.com/dist'
        );

        return $finder;
    }

    public function getFactory()
    {
        return new Factory(
            $this->getFinder()
        );
    }

    public function testAssetFinderCanAddLocations()
    {
        $finder = new Finder(new Filesystem());

        $finder->addLocation(
            '/home/www/htdocs/content/themes/themosis/dist',
            'https://www.website.com/content/themes/themosis/dist'
        );

        $finder->addLocations([
            'www/resources' => 'http://example.com/resources/',
            'public/assets/' => 'https://wordpress.xyz/assets',
            '/public/dist' => 'http://sub.domain.com/dist/'
        ]);

        $this->assertEquals(
            [
                '/home/www/htdocs/content/themes/themosis/dist',
                '/www/resources',
                '/public/assets',
                '/public/dist'
            ],
            array_keys($finder->getLocations())
        );

        $this->assertEquals([
            'https://www.website.com/content/themes/themosis/dist',
            'http://example.com/resources',
            'https://wordpress.xyz/assets',
            'http://sub.domain.com/dist'
        ], array_values($finder->getLocations()));
    }

    public function testAssetFinderFindLocalAssetFiles()
    {
        $finder = new Finder(new Filesystem());

        $finder->addLocation(
            __DIR__.'/files/',
            'https://www.domain.com/dist'
        );

        $file = $finder->find('theme.css');

        $this->assertEquals(__DIR__.'/files/theme.css', $file->getPath());
        $this->assertEquals('https://www.domain.com/dist/theme.css', $file->getUrl());
        $this->assertFalse($file->isExternal());

        $file = $finder->find('/js/carousel.js');

        $this->assertFalse($file->isExternal());
        $this->assertEquals(__DIR__.'/files/js/carousel.js', $file->getPath());
        $this->assertEquals('https://www.domain.com/dist/js/carousel.js', $file->getUrl());
    }

    public function testAssetFinderFindExternalAssets()
    {
        $finder = new Finder(new Filesystem());

        $file = $finder->find('https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js');

        $this->assertEmpty($file->getPath());
        $this->assertEquals('https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js', $file->getUrl());
        $this->assertTrue($file->isExternal());

        $file = $finder->find('https://fonts.googleapis.com/css?family=Roboto');

        $this->assertEmpty($file->getPath());
        $this->assertEquals('https://fonts.googleapis.com/css?family=Roboto', $file->getUrl());
        $this->assertTrue($file->isExternal());

        $file = $finder->find('https://use.typekit.net/xxxxxxx.css');

        $this->assertEmpty($file->getPath());
        $this->assertEquals('https://use.typekit.net/xxxxxxx.css', $file->getUrl());
        $this->assertTrue($file->isExternal());
    }

    public function testAssetsFactoryReturnAssetInstance()
    {
        $factory = $this->getFactory();

        $this->assertInstanceOf(
            Asset::class,
            $factory->add('ecommerce', 'css/products.min.css')
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->assertInstanceOf(
            AssetInterface::class,
            $factory->add('', '')
        );
    }

    public function testAddNewAssetsOnFrontEnd()
    {
        $factory = $this->getFactory();

        $asset = $factory->add('theme', 'theme.min.js');

        $this->assertEquals('theme', $asset->getHandle());
        $this->assertFalse($asset->file()->isExternal());
        $this->assertEquals(__DIR__.'/files/theme.min.js', $asset->getPath());
        $this->assertEquals('https://www.domain.com/dist/theme.min.js', $asset->getUrl());

        //Asset::add('handle', 'relative/path.css', false, 2.0, 'all')->to();
    }
}
