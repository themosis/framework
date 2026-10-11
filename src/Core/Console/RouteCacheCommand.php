<?php

namespace Themosis\Core\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\RouteCollection;
use Themosis\Core\Application;
use Themosis\Core\Support\Providers\RouteServiceProvider;
use Themosis\Route\Route;

class RouteCacheCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'route:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a route cache file for faster route registration';

    /**
     * @var Filesystem
     */
    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the command.
     */
    public function handle()
    {
        $this->call('route:clear');

        $app = $this->getFreshApplication();
        $routes = $this->getFreshApplicationRoutes($app);

        if (0 == count($routes)) {
            $this->error('Your application does not have any routes.');

            return;
        }

        foreach ($routes as $route) {
            /** @var Route $route */
            $route->prepareForSerialization();
        }

        $this->files->put(
            $this->laravel->getCachedRoutesPath(),
            $this->buildRouteCacheFile($routes, $this->getRouteProviders($app)),
        );

        $this->info('Routes cached successfully.');
    }

    /**
     * Boot a fresh copy of the application and retrieve its routes.
     *
     * @return RouteCollection
     */
    protected function getFreshApplicationRoutes($app = null)
    {
        $app ??= $this->getFreshApplication();

        return tap($app['router']->getRoutes(), function ($routes) {
            /** @var RouteCollection $routes */
            $routes->refreshNameLookups();
            $routes->refreshActionLookups();
        });
    }

    /**
     * Return a fresh application instance.
     *
     * @return Application
     */
    protected function getFreshApplication()
    {
        return tap(require $this->laravel->bootstrapPath('app.php'), function ($app) {
            $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        });
    }

    /**
     * Build the route cache file.
     *
     * @param RouteCollection $routes
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     *
     * @return string
     */
    protected function buildRouteCacheFile(RouteCollection $routes, array $providers = [])
    {
        $stub = $this->files->get(__DIR__ . '/stubs/routes.stub');

        return str_replace(
            ['{{routes}}', '{{providers}}'],
            [base64_encode(serialize($routes)), var_export($providers, true)],
            $stub,
        );
    }

    /**
     * Get the route service providers whose routes are part of the cache.
     *
     * Theme route providers are only registered once WordPress loads the theme,
     * so they are not part of the cache and keep loading their routes at runtime.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return array<int, class-string>
     */
    protected function getRouteProviders($app): array
    {
        return array_values(array_filter(
            array_keys($app->getLoadedProviders()),
            fn ($provider) => is_a($provider, RouteServiceProvider::class, true),
        ));
    }
}
