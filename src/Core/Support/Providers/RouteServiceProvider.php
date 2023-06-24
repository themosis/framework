<?php

namespace Themosis\Core\Support\Providers;

use Closure;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Traits\ForwardsCalls;
use Themosis\Route\Router;

class RouteServiceProvider extends ServiceProvider
{
    use ForwardsCalls;

    /**
     * Controller namespace used by loaded routes.
     *
     * @var string|null
     */
    protected $namespace;

    /**
     * The callback that should be used to load application's routes.
     *
     * @var \Closure|null
     */
    protected $loadRoutesUsing;

    public function register()
    {
        $this->booted(function () {
            $this->setRootControllerNamespace();

            if ($this->routesAreCached()) {
                $this->loadCachedRoutes();
            } else {
                $this->loadRoutes();

                $this->app->booted(function () {
                    $this->app['router']->getRoutes()->refreshNameLookups();
                    $this->app['router']->getRoutes()->refreshActionLookups();
                });
            }
        });
    }

    /**
     * Set the root controller namespace for the application.
     */
    protected function setRootControllerNamespace()
    {
        if (! is_null($this->namespace)) {
            $this->app[UrlGenerator::class]->setRootControllerNamespace($this->namespace);
        }
    }

    /**
     * Determine if the application routes are cached.
     *
     * @return bool
     */
    protected function routesAreCached()
    {
        return $this->app->routesAreCached();
    }

    /**
     * Load the cached routes for the application.
     */
    protected function loadCachedRoutes()
    {
        $this->app->booted(function () {
            require $this->app->getCachedRoutesPath();
        });
    }

    /**
     * Load the application routes.
     */
    protected function loadRoutes()
    {
        if (! is_null($this->loadRoutesUsing)) {
            $this->app->call($this->loadRoutesUsing);
        } elseif (method_exists($this, 'map')) {
            $this->app->call([$this, 'map']);
        }
    }

    /**
     * Bootstrap the service.
     */
    public function boot()
    {
        //
    }

    /**
     * Pass dynamic methods onto the router instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __call($method, $parameters)
    {
        return $this->forwardCallTo(
            $this->app->make(Router::class),
            $method,
            $parameters,
        );
    }

    /**
     * Register the callback that will be used to load the application's routes.
     *
     *
     * @return $this
     */
    protected function routes(Closure $routesCallback)
    {
        $this->loadRoutesUsing = $routesCallback;

        return $this;
    }
}
