<?php

namespace Themosis\Core\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\RouteCollection;
use Themosis\Core\Application;
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

        $routes = $this->getFreshApplicationRoutes();

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
            $this->buildRouteCacheFile($routes)
        );

        $this->info('Routes cached successfully.');
    }

    /**
     * Boot a fresh copy of the application and retrieve its routes.
     *
     * @return RouteCollection
     */
    protected function getFreshApplicationRoutes()
    {
        return tap($this->getFreshApplication()['router']->getRoutes(), function ($routes) {
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
    protected function buildRouteCacheFile(RouteCollection $routes)
    {
        $stub = $this->files->get(__DIR__.'/stubs/routes.stub');

        return str_replace('{{routes}}', base64_encode(serialize($routes)), $stub);
    }
}
