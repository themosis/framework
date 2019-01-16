<?php

namespace Themosis\Tests\Core;

use Illuminate\Container\Container;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Log\LogServiceProvider;
use Illuminate\Support\ServiceProvider;
use PHPUnit\Framework\TestCase;
use Themosis\Core\Application;
use Themosis\Core\PackageManifest;
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
            $path.DS.'app',
            $app['path'],
            'Cannot get the default path'
        );
        $this->assertEquals(
            $path,
            $app['path.base'],
            'Cannot get the base path'
        );
        $this->assertEquals(
            $path.DS.'htdocs'.DS.'content',
            $app['path.content'],
            'Cannot get the content path'
        );
        $this->assertEquals(
            $path.DS.'htdocs'.DS.'content'.DS.'mu-plugins',
            $app['path.muplugins'],
            'Cannot get the mu-plugins path'
        );
        $this->assertEquals(
            $path.DS.'htdocs'.DS.'content'.DS.'plugins',
            $app['path.plugins'],
            'Cannot get the plugins path'
        );
        $this->assertEquals(
            $path.DS.'htdocs'.DS.'content'.DS.'themes',
            $app['path.themes'],
            'Cannot get the themes path'
        );
        $this->assertEquals(
            $path.DS.'app',
            $app['path.application'],
            'Cannot get the app path'
        );
        $this->assertEquals(
            $path.DS.'resources',
            $app['path.resources']
        );
        $this->assertEquals(
            $path.DS.'resources'.DS.'languages',
            $app['path.lang'],
            'Cannot get the languages path'
        );
        $this->assertEquals(
            $path.DS.'htdocs',
            $app['path.web'],
            'Cannot get the web path'
        );
        $this->assertEquals(
            $path,
            $app['path.root'],
            'Cannot get the root path'
        );
        $this->assertEquals(
            $path.DS.'config',
            $app['path.config'],
            'Cannot get the defaut config path'
        );
        $this->assertEquals(
            $path.DS.'htdocs',
            $app['path.public'],
            'Cannot get the public path'
        );
        $this->assertEquals(
            $path.DS.'storage',
            $app['path.storage'],
            'Cannot get the storage path'
        );
        $this->assertEquals(
            $path.DS.'database',
            $app['path.database'],
            'Cannot get the database path'
        );
        $this->assertEquals(
            $path.DS.'bootstrap',
            $app['path.bootstrap'],
            'Cannot get the bootstrap path'
        );
        $this->assertEquals(
            $path.DS.'htdocs'.DS.'cms',
            $app['path.wp'],
            'Cannot get the WordPress path'
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
    }

    public function testServiceProvidersAreCorrectlyRegistered()
    {
        $app = new Application();
        $provider = $this->getMockBuilder('BasicServiceProvider')->setMethods(['register', 'boot'])->getMock();
        $class = get_class($provider);
        $provider->expects($this->once())->method('register');
        $app->register($provider);

        $this->assertTrue(in_array($class, $app->getLoadedProviders()));
    }

    public function testClassesAreBoundWhenServiceProviderIsRegistered()
    {
        $app = new Application();
        $provider = new ServiceProviderForTestingThree($app);
        $app->register($provider);

        $this->assertTrue(in_array(get_class($provider), $app->getLoadedProviders()));
        $this->assertInstanceOf(ConcreteClass::class, $app->make(AbstractClass::class));
    }

    public function testSingletonsAreCreatedWhenServiceProviderIsRegistered()
    {
        $app = new Application();
        $provider = new ServiceProviderForTestingThree($app);
        $app->register($provider);

        $this->assertTrue(in_array(get_class($provider), $app->getLoadedProviders()));
        $instance = $app->make(AbstractClass::class);
        $this->assertSame($instance, $app->make(AbstractClass::class));
    }

    public function testDeferredServicesMarkedAsBound()
    {
        $app = new Application();
        $app->setDeferredServices([
            'foo' => 'Themosis\Tests\Core\ApplicationDeferredServiceStub'
        ]);

        $this->assertTrue($app->bound('foo'));
        $this->assertEquals('foo', $app->make('foo'));
    }

    public function testDeferredServicesAreShared()
    {
        $app = new Application();
        $app->setDeferredServices([
            'foo' => 'Themosis\Tests\Core\ApplicationDeferredSharedService'
        ]);
        $this->assertTrue($app->bound('foo'));

        $one = $app->make('foo');
        $two = $app->make('foo');

        $this->assertInstanceOf('stdClass', $one);
        $this->assertInstanceOf('stdClass', $two);
        $this->assertSame($one, $two);
    }

    public function testDeferredServiceCanBeExtended()
    {
        $app = new Application();
        $app->setDeferredServices([
            'foo' => 'Themosis\Tests\Core\ApplicationDeferredServiceStub'
        ]);
        $app->extend('foo', function ($instance, $container) {
            return $instance.'bar';
        });

        $this->assertEquals('foobar', $app->make('foo'));
    }

    public function testDeferredServiceProviderIsRegisteredOnlyOnce()
    {
        $app = new Application();
        $app->setDeferredServices([
            'foo' => 'Themosis\Tests\Core\ApplicationDeferredServiceCountStub'
        ]);
        $instance = $app->make('foo');
        $this->assertInstanceOf('stdClass', $instance);
        $this->assertSame($instance, $app->make('foo'));
        $this->assertEquals(1, ApplicationDeferredServiceCountStub::$count);
    }

    public function testDeferredServiceDontRunWhenInstanceSet()
    {
        $app = new Application();
        $app->setDeferredServices([
            'foo' => 'Themosis\Tests\Core\ApplicationDeferredServiceStub'
        ]);
        $app->instance('foo', 'bar');
        $instance = $app->make('foo');
        $this->assertEquals($instance, 'bar');
    }

    public function testDeferredServicesAreLazilyInitialized()
    {
        ApplicationDeferredServiceStub::$initialized = false;
        $app = new Application();
        $app->setDeferredServices([
            'foo' => 'Themosis\Tests\Core\ApplicationDeferredServiceStub'
        ]);
        $this->assertTrue($app->bound('foo'));
        $this->assertFalse(ApplicationDeferredServiceStub::$initialized);

        $app->extend('foo', function ($instance) {
            return $instance.'bar';
        });

        $this->assertFalse(ApplicationDeferredServiceStub::$initialized);
        $this->assertEquals('foobar', $app->make('foo'));
        $this->assertTrue(ApplicationDeferredServiceStub::$initialized);
    }

    public function testDeferredServicesCanRegisterFactories()
    {
        $app = new Application();
        $app->setDeferredServices([
            'foo' => 'Themosis\Tests\Core\ApplicationFactoryServiceProvider'
        ]);

        $this->assertTrue($app->bound('foo'));
        $this->assertEquals(1, $app->make('foo'));
        $this->assertEquals(2, $app->make('foo'));
        $this->assertEquals(3, $app->make('foo'));
    }

    public function testSingleProviderCanProvideMultipleDeferredServices()
    {
        $app = new Application();
        $app->setDeferredServices([
            'foo' => 'Themosis\Tests\Core\ApplicationMultiProvider',
            'bar' => 'Themosis\Tests\Core\ApplicationMultiProvider'
        ]);

        $this->assertEquals('foo', $app->make('foo'));
        $this->assertEquals('foobar', $app->make('bar'));
    }

    public function testEnvironment()
    {
        $app = new Application();
        $app['env'] = 'foo';

        $this->assertEquals('foo', $app->environment());

        $this->assertTrue($app->environment('foo'));
        $this->assertTrue($app->environment('f*'));
        $this->assertTrue($app->environment('foo', 'bar'));
        $this->assertTrue($app->environment(['foo', 'bar']));
        $this->assertFalse($app->environment('qux'));
        $this->assertFalse($app->environment('q*'));
        $this->assertFalse($app->environment('qux', 'bar'));
        $this->assertFalse($app->environment(['qux', 'bar']));
    }

    public function testMethodAfterLoadingEnvironmentAddsClosure()
    {
        $app = new Application();
        $closure = function () {
        };
        $app->afterLoadingEnvironment($closure);
        $this->assertArrayHasKey(
            0,
            $app['events']->getListeners('bootstrapped: Themosis\Core\Bootstrap\EnvironmentLoader')
        );
    }

    public function testBeforeBootstrappingMethodAddsClosure()
    {
        $app = new Application();
        $closure = function () {
        };
        $app->beforeBootstrapping('Themosis\Core\Bootstrap\RegisterFacades', $closure);
        $this->assertArrayHasKey(
            0,
            $app['events']->getListeners('bootstrapping: Themosis\Core\Bootstrap\RegisterFacades')
        );
    }

    public function testAfterBootstrappingMethodAddsClosure()
    {
        $app = new Application();
        $closure = function () {
        };
        $app->afterBootstrapping('Themosis\Core\Bootstrap\RegisterFacades', $closure);
        $this->assertArrayHasKey(
            0,
            $app['events']->getListeners('bootstrapped: Themosis\Core\Bootstrap\RegisterFacades')
        );
    }
}

class ApplicationMultiProvider extends ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->singleton('foo', function () {
            return 'foo';
        });

        $this->app->singleton('bar', function ($app) {
            return $app['foo'].'bar';
        });
    }
}

class ApplicationFactoryServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->bind('foo', function () {
            static $count = 0;

            return ++$count;
        });
    }
}

class ApplicationDeferredServiceCountStub extends ServiceProvider
{
    public static $count = 0;

    protected $defer = true;

    public function register()
    {
        static::$count++;
        $this->app['foo'] = new \stdClass();
    }
}

class ApplicationDeferredSharedService extends ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->singleton('foo', function () {
            return new \stdClass();
        });
    }
}

class ApplicationDeferredServiceStub extends ServiceProvider
{
    public static $initialized = false;

    protected $defer = true;

    public function register()
    {
        static::$initialized = true;
        $this->app['foo'] = 'foo';
    }
}

class ServiceProviderWithNoRegisterMethod extends ServiceProvider
{
}

class ServiceProviderForTestingThree extends ServiceProvider
{
    public $bindings = [
        AbstractClass::class => ConcreteClass::class
    ];

    public $singletons = [
        AbstractClass::class => ConcreteClass::class
    ];

    public function register()
    {
    }

    public function boot()
    {
    }
}

abstract class AbstractClass
{
    //
}

class ConcreteClass extends AbstractClass
{
    //
}
