<?php

namespace Themosis\Tests\Core;

use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Themosis\Core\Application;
use Themosis\Core\PackageManifest;
use Themosis\Hook\Hookable;

class ApplicationLifecycleTest extends TestCase
{
    /**
     * Bootstrap-relative cache file locations resolved by the application.
     */
    private const CONFIG_CACHE_PATH = 'bootstrap/cache/config.php';
    private const EVENTS_CACHE_PATH = 'bootstrap/cache/events.php';
    private const ROUTES_CACHE_PATH = 'bootstrap/cache/routes.php';

    private string $basePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->basePath = realpath(__DIR__ . '/../');
    }

    /**
     * Default fixture: a fresh application rooted at the tests directory.
     */
    private function makeApplication(): Application
    {
        $app = new Application($this->basePath);

        // The filesystem is normally bound by the FilesystemServiceProvider; the
        // cache contracts (routesAreCached/eventsAreCached) resolve 'files' directly.
        $app->instance('files', new Filesystem());

        return $app;
    }

    /**
     * Resolve a bootstrap-relative cache path to its expected absolute form.
     */
    private function expectedPath(string $relative): string
    {
        return $this->basePath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
    }

    /*
    |--------------------------------------------------------------------------
    | Boot lifecycle
    |--------------------------------------------------------------------------
    */

    public function testApplicationIsNotBootedByDefault()
    {
        $this->assertFalse($this->makeApplication()->isBooted());
    }

    public function testBootFiresBootingThenBootedCallbacksAndMarksApplicationBooted()
    {
        $app = $this->makeApplication();
        $sequence = [];

        $app->booting(function ($passed) use (&$sequence, $app) {
            $sequence[] = 'booting';
            $this->assertSame($app, $passed, 'Booting callback should receive the application.');
        });
        $app->booted(function ($passed) use (&$sequence, $app) {
            $sequence[] = 'booted';
            $this->assertSame($app, $passed, 'Booted callback should receive the application.');
        });

        $app->boot();

        $this->assertTrue($app->isBooted());
        $this->assertSame(['booting', 'booted'], $sequence);
    }

    public function testBootOnlyRunsOnce()
    {
        $app = $this->makeApplication();
        $bootingCount = 0;

        $app->booting(function () use (&$bootingCount) {
            $bootingCount++;
        });

        $app->boot();
        $app->boot();

        $this->assertSame(1, $bootingCount);
    }

    public function testBootedCallbackFiresImmediatelyWhenApplicationAlreadyBooted()
    {
        $app = $this->makeApplication();
        $app->boot();

        $fired = false;
        $app->booted(function () use (&$fired) {
            $fired = true;
        });

        $this->assertTrue($fired);
    }

    /*
    |--------------------------------------------------------------------------
    | Caching contracts
    |--------------------------------------------------------------------------
    */

    public function testConfigurationIsNotCachedByDefault()
    {
        $this->assertFalse($this->makeApplication()->configurationIsCached());
    }

    public function testRoutesAreNotCachedByDefault()
    {
        $this->assertFalse($this->makeApplication()->routesAreCached());
    }

    public function testEventsAreNotCachedByDefault()
    {
        $this->assertFalse($this->makeApplication()->eventsAreCached());
    }

    public function testCachedConfigPathPointsToBootstrapCacheLocation()
    {
        $this->assertSame(
            $this->expectedPath(self::CONFIG_CACHE_PATH),
            $this->makeApplication()->getCachedConfigPath(),
        );
    }

    public function testCachedRoutesPathPointsToBootstrapCacheLocation()
    {
        $this->assertSame(
            $this->expectedPath(self::ROUTES_CACHE_PATH),
            $this->makeApplication()->getCachedRoutesPath(),
        );
    }

    public function testCachedEventsPathPointsToBootstrapCacheLocation()
    {
        $this->assertSame(
            $this->expectedPath(self::EVENTS_CACHE_PATH),
            $this->makeApplication()->getCachedEventsPath(),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | WordPress hook registration
    |--------------------------------------------------------------------------
    */

    public function testRegisterHookCallsRegisterOnHookableWithoutBoundHooks()
    {
        HookableWithoutHooks::$registered = false;

        $this->makeApplication()->registerHook(HookableWithoutHooks::class);

        $this->assertTrue(HookableWithoutHooks::$registered);
    }

    public function testRegisterHookIgnoresHookableWithoutRegisterMethod()
    {
        $this->assertNull(
            $this->makeApplication()->registerHook(HookableWithoutRegister::class),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Console / unit-test context
    |--------------------------------------------------------------------------
    */

    public function testRunningInConsoleReturnsTrueWhenInCliSapi()
    {
        // PHPUnit executes via the CLI SAPI, so this is always true in this suite.
        $this->assertTrue($this->makeApplication()->runningInConsole());
    }

    public function testRunningUnitTestsReturnsTrueWhenEnvironmentIsTesting()
    {
        $app = $this->makeApplication();
        $app->detectEnvironment(fn () => 'testing');

        $this->assertTrue($app->runningUnitTests());
    }

    public function testRunningUnitTestsReturnsFalseForNonTestingEnvironment()
    {
        $app = $this->makeApplication();
        $app->detectEnvironment(fn () => 'production');

        $this->assertFalse($app->runningUnitTests());
    }

    /*
    |--------------------------------------------------------------------------
    | registerConfiguredProviders
    |--------------------------------------------------------------------------
    */

    public function testRegisterConfiguredProvidersLoadsProvidersFromConfig()
    {
        $app = $this->makeApplication();

        $app->instance('config', new ConfigRepository([
            'app' => ['providers' => [EventServiceProvider::class]],
        ]));

        $manifest = $this->createMock(PackageManifest::class);
        $manifest->method('providers')->willReturn([]);
        $app->instance(PackageManifest::class, $manifest);

        $cacheDir = sys_get_temp_dir() . '/themosis_providers_' . uniqid();
        mkdir($cacheDir, 0755, true);
        putenv("APP_SERVICES_CACHE={$cacheDir}/services.php");

        try {
            $app->registerConfiguredProviders();

            $this->assertArrayHasKey(EventServiceProvider::class, $app->getLoadedProviders());
        } finally {
            putenv('APP_SERVICES_CACHE');

            if (file_exists("{$cacheDir}/services.php")) {
                unlink("{$cacheDir}/services.php");
            }

            rmdir($cacheDir);
        }
    }
}

class HookableWithoutHooks extends Hookable
{
    public static $registered = false;

    public function register()
    {
        static::$registered = true;
    }
}

class HookableWithoutRegister extends Hookable
{
}
