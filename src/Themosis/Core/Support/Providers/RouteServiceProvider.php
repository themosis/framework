<?php

namespace Themosis\Core\Support\Providers;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;
use Themosis\Route\Router;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Controller namespace used by loaded routes.
     *
     * @var string
     */
    protected $namespace;

    /**
     * Bootstrap the service.
     */
    public function boot()
    {
        $this->setControllerNamespace();

        $this->loadRoutes();
    }

    /**
     * Set the controller namespace.
     */
    protected function setControllerNamespace()
    {
        if (! is_null($this->namespace)) {
            $this->app[UrlGenerator::class]->setRootControllerNamespace($this->namespace);
        }
    }

    /**
     * Load routes.
     */
    protected function loadRoutes()
    {
        if (method_exists($this, 'map')) {
            $this->app->call([$this, 'map']);
        }
    }

    /**
     * Pass dynamic methods onto the router instance.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array(
            [$this->app->make(Router::class), $method],
            $parameters
        );
    }

    public function register()
    {
        //
    }
}
