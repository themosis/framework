<?php

namespace Themosis\Tests\Assets;

use Illuminate\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Themosis\Asset\Asset;
use Themosis\Asset\AssetInterface;
use Themosis\Asset\Factory;
use Themosis\Asset\Finder;
use Themosis\Core\Application;
use Themosis\Hook\ActionBuilder;
use Themosis\Hook\FilterBuilder;
use Themosis\Html\HtmlBuilder;

class AssetsTest extends TestCase
{
    public function getFinder()
    {
        $finder = new Finder(new Filesystem());
        $finder->addLocation(
            __DIR__.'/files',
            'https://www.domain.com/dist',
        );

        return $finder;
    }

    public function getFactory()
    {
        $app = new Application();

        return new Factory(
            $this->getFinder(),
            new ActionBuilder($app),
            new FilterBuilder($app),
            new HtmlBuilder(),
        );
    }

    public function testAssetFinderCanAddLocations()
    {
        $finder = new Finder(new Filesystem());

        $finder->addLocation(
            '/home/www/htdocs/content/themes/themosis/dist',
            'https://www.website.com/content/themes/themosis/dist',
        );

        $finder->addLocations([
            'www/resources' => 'http://example.com/resources/',
            'public/assets/' => 'https://wordpress.xyz/assets',
            '/public/dist' => 'http://sub.domain.com/dist/',
            'c:\\dev\\sites\\project-x\\public\\dist' => 'http://project-x.com/dist',
        ]);

        $this->assertEquals(
            [
                '/home/www/htdocs/content/themes/themosis/dist',
                'www/resources',
                'public/assets',
                '/public/dist',
                'c:\\dev\\sites\\project-x\\public\\dist',
            ],
            array_keys($finder->getLocations()),
        );

        $this->assertEquals([
            'https://www.website.com/content/themes/themosis/dist',
            'http://example.com/resources',
            'https://wordpress.xyz/assets',
            'http://sub.domain.com/dist',
            'http://project-x.com/dist',
        ], array_values($finder->getLocations()));
    }

    public function testAssetFinderFindLocalAssetFiles()
    {
        $finder = new Finder(new Filesystem());

        $finder->addLocation(
            __DIR__.'/files/',
            'https://www.domain.com/dist',
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
            $factory->add('ecommerce', 'css/products.min.css'),
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->assertInstanceOf(
            AssetInterface::class,
            $factory->add('', ''),
        );
    }

    public function testAddAssetWithDefaults()
    {
        $factory = $this->getFactory();

        // Local
        $asset = $factory->add('theme', 'theme.min.js');

        $this->assertEquals('theme', $asset->getHandle());
        $this->assertFalse($asset->file()->isExternal());
        $this->assertEquals(__DIR__.'/files/theme.min.js', $asset->getPath());
        $this->assertEquals('https://www.domain.com/dist/theme.min.js', $asset->getUrl());
        $this->assertEmpty($asset->getDependencies());
        $this->assertNull($asset->getVersion());
        $this->assertEquals('script', $asset->getType());

        // External
        $asset = $factory->add('typekit', 'https://use.typekit.net/xxxxxxx.css');

        $this->assertEquals('typekit', $asset->getHandle());
        $this->assertTrue($asset->file()->isExternal());
        $this->assertEmpty($asset->getPath());
        $this->assertEquals('https://use.typekit.net/xxxxxxx.css', $asset->getUrl());
        $this->assertEmpty($asset->getDependencies());
        $this->assertNull($asset->getVersion());
        $this->assertEquals('style', $asset->getType());
    }

    public function testAddAssetsWithDependencies()
    {
        $factory = $this->getFactory();

        $asset = $factory->add('theme', 'theme.min.js', ['jquery']);

        $this->assertEquals(['jquery'], $asset->getDependencies());

        $asset = $factory->add('products', 'css/products.min.css', ['bootstrap', 'jqueryui']);

        $this->assertEquals([
            'bootstrap',
            'jqueryui',
        ], $asset->getDependencies());
    }

    public function testAddAssetsWithVersioning()
    {
        $factory = $this->getFactory();

        $asset = $factory->add('theme', 'theme.css', [], '1.0');

        $this->assertEquals('1.0', $asset->getVersion());

        $asset = $factory->add('carousel', 'js/carousel.js', [], false);

        $this->assertFalse($asset->getVersion());
    }

    public function testAddAssetsAndDiscoverOrSetFileType()
    {
        $factory = $this->getFactory();

        // Local js
        $asset = $factory->add('theme', 'theme.min.js');
        $this->assertEquals('script', $asset->getType());

        // Local css
        $asset = $factory->add('products', 'css/products.min.css');
        $this->assertEquals('style', $asset->getType());

        // External js
        $asset = $factory->add('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js');
        $this->assertEquals('script', $asset->getType());

        // External css
        $asset = $factory->add('typekit', 'https://use.typekit.net/xxxxxxx.css');
        $this->assertEquals('style', $asset->getType());

        // External - Defined
        $asset = $factory->add('font', 'https://fonts.googleapis.com/css?family=Roboto');

        $this->assertNull($asset->getType());
        $asset->setType('css');
        $this->assertEquals('style', $asset->getType());
    }

    public function testAddAssetsWithCustomArgument()
    {
        $factory = $this->getFactory();

        // Local JS - Default
        $asset = $factory->add('theme', '/theme.min.js');
        $this->assertTrue($asset->getArgument());

        // Local JS - Defined for footer
        $asset = $factory->add('theme', 'theme.min.js', [], false, true);
        $this->assertTrue($asset->getArgument());

        // Local JS - Defined for head
        $asset = $factory->add('carousel', 'js/carousel.js', [], false, false);
        $this->assertFalse($asset->getArgument());

        // Local CSS - Default
        $asset = $factory->add('theme', 'theme.css');
        $this->assertEquals('all', $asset->getArgument());

        // Local CSS - Custom
        $asset = $factory->add('theme', 'theme.css', [], false, 'screen');
        $this->assertEquals('screen', $asset->getArgument());

        // External JS - With Extension
        $asset = $factory->add('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js');
        $this->assertTrue($asset->getArgument());

        // External JS - Without extension (default)
        $asset = $factory->add('custom', '//api.domain.com/awesomescript');
        $this->assertNull($asset->getArgument());

        // External JS - Without extension for the footer.
        $asset = $factory->add('custom', '//api.domain.com/somescript', [], 2.0, true);
        $this->assertTrue($asset->getArgument());

        // Extermal CSS - Without extension.
        $asset = $factory->add(
            'bootstrap',
            'https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap',
            [],
            4.1,
        );
        $this->assertNull($asset->getArgument());
        $asset->setArgument('screen');
        $this->assertEquals('screen', $asset->getArgument());
    }
}
