<?php

namespace Themosis\Tests\Core;

use Illuminate\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Themosis\Core\Application;
use Themosis\Core\Support\Providers\RouteServiceProvider;

class RouteCacheProvidersTest extends TestCase
{
    private string $cacheFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cacheFile = sys_get_temp_dir() . '/themosis_routes_' . uniqid() . '.php';
        $_ENV['APP_ROUTES_CACHE'] = $_SERVER['APP_ROUTES_CACHE'] = $this->cacheFile;
        $GLOBALS['themosis_route_cache_loads'] = 0;
    }

    protected function tearDown(): void
    {
        unset($_ENV['APP_ROUTES_CACHE'], $_SERVER['APP_ROUTES_CACHE'], $GLOBALS['themosis_route_cache_loads']);

        if (is_file($this->cacheFile)) {
            unlink($this->cacheFile);
        }

        parent::tearDown();
    }

    public function testProvidersMissingFromTheRouteCacheStillLoadTheirRoutes()
    {
        file_put_contents($this->cacheFile, sprintf(
            '<?php $GLOBALS["themosis_route_cache_loads"]++;
            app()->instance("routes.cached.providers", [%s::class]);
            app("router")->get("cached", fn () => "cached");',
            CachedRoutesServiceProvider::class,
        ));

        $app = new Application(realpath(__DIR__ . '/../'));
        $app->instance('files', new Filesystem());

        $app->register(CachedRoutesServiceProvider::class);
        $app->boot();

        // Registered once the application is booted, like a theme provider.
        $app->register(ThemeRoutesServiceProvider::class);

        $uris = array_map(fn ($route) => $route->uri(), $app['router']->getRoutes()->getRoutes());

        $this->assertContains('cached', $uris);
        $this->assertContains('theme', $uris);
        $this->assertNotContains('app-uncached', $uris, 'Cached providers must not load their route files.');
        $this->assertSame(1, $GLOBALS['themosis_route_cache_loads'], 'The route cache file must be loaded once.');
    }
}

class CachedRoutesServiceProvider extends RouteServiceProvider
{
    public function map()
    {
        $this->app['router']->get('app-uncached', fn () => 'app');
    }
}

class ThemeRoutesServiceProvider extends RouteServiceProvider
{
    public function map()
    {
        $this->app['router']->get('theme', fn () => 'theme');
    }
}
