<?php

use Illuminate\Container\Container;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Log\LogServiceProvider;
use PHPUnit\Framework\TestCase;
use Themosis\Core\Application;
use Themosis\Core\PackageManifest;
use Themosis\Hook\HookServiceProvider;
use Themosis\Route\RouteServiceProvider;

class ApplicationTest extends TestCase
{
    public function testBasePathSetup()
    {
        $path = realpath(__DIR__.'/../../');
        $app = new Application($path);
        $this->assertEquals($path, $app->basePath());
    }

    public function testApplicationPaths()
    {
        $path = realpath(__DIR__.'/../');
        $app = new Application($path);

        $this->assertEquals(
            $path.'/app',
            $app['path'],
            'Cannot get the default path'
        );
        $this->assertEquals(
            $path,
            $app['path.base'],
            'Cannot get the base path'
        );
        $this->assertEquals(
            $path.'/htdocs/content',
            $app['path.content'],
            'Cannot get the content path'
        );
        $this->assertEquals(
            $path.'/htdocs/content/mu-plugins',
            $app['path.muplugins'],
            'Cannot get the mu-plugins path'
        );
        $this->assertEquals(
            $path.'/htdocs/content/plugins',
            $app['path.plugins'],
            'Cannot get the plugins path'
        );
        $this->assertEquals(
            $path.'/htdocs/content/themes',
            $app['path.themes'],
            'Cannot get the themes path'
        );
        $this->assertEquals(
            $path.'/app',
            $app['path.application'],
            'Cannot get the app path'
        );
        $this->assertEquals(
            $path.'/htdocs/content/languages',
            $app['path.lang'],
            'Cannot get the languages path'
        );
        $this->assertEquals(
            $path.'/htdocs',
            $app['path.web'],
            'Cannot get the web path'
        );
        $this->assertEquals(
            $path,
            $app['path.root'],
            'Cannot get the root path'
        );
        $this->assertEquals(
            $path.'/config',
            $app['path.config'],
            'Cannot get the defaut config path'
        );
        $this->assertEquals(
            $path.'/htdocs',
            $app['path.public'],
            'Cannot get the public path'
        );
        $this->assertEquals(
            $path.'/storage',
            $app['path.storage'],
            'Cannot get the storage path'
        );
        $this->assertEquals(
            $path.'/database',
            $app['path.database'],
            'Cannot get the database path'
        );
        $this->assertEquals(
            $path.'/bootstrap',
            $app['path.bootstrap'],
            'Cannot get the bootstrap path'
        );
    }

    public function testApplicationBaseBindings()
    {
        $path = realpath(__DIR__.'/../');
        $app = new Application($path);

        $this->assertInstanceOf(
            'Themosis\Core\Application',
            $app['app'],
            'Application instance is not bound'
        );
        $this->assertInstanceOf(
            Container::class,
            $app['Illuminate\Container\Container'],
            'Container instance is not bound'
        );
        $this->assertInstanceOf(
            PackageManifest::class,
            $app['Themosis\Core\PackageManifest'],
            'Package manifest is not bound'
        );
    }

    public function testApplicationBaseServiceProviders()
    {
        $path = realpath(__DIR__.'/../');
        $app = new Application($path);

        $this->assertInstanceOf(
            'Illuminate\Events\EventServiceProvider',
            $app->getProvider(EventServiceProvider::class),
            'The event service provider is not registered'
        );
        $this->assertInstanceOf(
            'Illuminate\Log\LogServiceProvider',
            $app->getProvider(LogServiceProvider::class),
            'Log service provider is not registered'
        );
        $this->assertInstanceOf(
            'Themosis\Route\RouteServiceProvider',
            $app->getProvider(RouteServiceProvider::class),
            'Route service provider is not registered'
        );
        $this->assertInstanceOf(
            'Themosis\Hook\HookServiceProvider',
            $app->getProvider(HookServiceProvider::class),
            'Hook service provider is not registered'
        );
    }
}
