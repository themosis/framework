<?php

namespace Themosis\Core\Http;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Response as IlluminateResponse;
use Illuminate\Routing\Pipeline;
use Illuminate\Support\Facades\Facade;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Symfony\Component\HttpFoundation\Response;
use Themosis\Core\Exceptions\Handler;
use Themosis\Core\Http\Events\RequestHandled;
use Themosis\Route\Router;
use Throwable;

class Kernel implements \Illuminate\Contracts\Http\Kernel
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Router
     */
    protected $router;

    /**
     * List of bootstrap classes.
     *
     * @var array
     */
    protected $bootstrappers = [
        \Themosis\Core\Bootstrap\EnvironmentLoader::class,
        \Themosis\Core\Bootstrap\ConfigurationLoader::class,
        \Themosis\Core\Bootstrap\ExceptionHandler::class,
        \Themosis\Core\Bootstrap\RegisterFacades::class,
        \Themosis\Core\Bootstrap\RegisterProviders::class,
        \Themosis\Core\Bootstrap\BootProviders::class
    ];

    /**
     * Application middleware stack. Used on every request.
     *
     * @var array
     */
    protected $middleware = [];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [];

    /**
     * Aliased middleware. Can be used individually or within groups.
     *
     * @var array
     */
    protected $routeMiddleware = [];

    /**
     * Priority-sorted list of middleware.
     *
     * @var array
     */
    protected $middlewarePriority = [
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class
    ];

    public function __construct(Application $app, Router $router)
    {
        $this->app = $app;
        $this->router = $router;

        $router->middlewarePriority = $this->middlewarePriority;

        foreach ($this->middlewareGroups as $key => $middleware) {
            $router->middlewareGroup($key, $middleware);
        }

        foreach ($this->routeMiddleware as $key => $middleware) {
            $router->aliasMiddleware($key, $middleware);
        }
    }

    /**
     * Initialize the kernel (bootstrap application base components).
     *
     * @param \Illuminate\Http\Request $request
     */
    public function init($request)
    {
        $this->app->instance('request', $request);
        Facade::clearResolvedInstance('request');
        $this->bootstrap();
    }

    /**
     * Handle an incoming HTTP request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return IlluminateResponse
     */
    public function handle($request)
    {
        try {
            $request->enableHttpMethodParameterOverride();
            $response = $this->sendRequestThroughRouter($request);
        } catch (Exception $e) {
            $this->reportException($e);
            $response = $this->renderException($request, $e);
        } catch (Throwable $e) {
            $this->reportException($e = new FatalThrowableError($e));
            $response = $this->renderException($request, $e);
        }

        $this->app['events']->dispatch(
            new RequestHandled($request, $response)
        );

        return $response;
    }

    /**
     * Determine if the kernel has a given middleware.
     *
     * @param string $middleware
     *
     * @return bool
     */
    public function hasMiddleware($middleware)
    {
        return in_array($middleware, $this->middleware);
    }

    /**
     * Send given request through the middleware (if any) and router.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return IlluminateResponse
     */
    protected function sendRequestThroughRouter($request)
    {
        return (new Pipeline($this->app))
            ->send($request)
            ->through($this->app->shouldSkipMiddleware() ? [] : $this->middleware)
            ->then($this->dispatchToRouter());
    }

    /**
     * Get route dispatcher callback used by the
     * routing pipeline.
     *
     * @return \Closure
     */
    protected function dispatchToRouter()
    {
        return function ($request) {
            $this->app->instance('request', $request);

            return $this->router->dispatch($request);
        };
    }

    /**
     * Bootstrap the application.
     */
    public function bootstrap()
    {
        if (! $this->app->hasBeenBootstrapped()) {
            $this->app->bootstrapWith($this->bootstrappers());
        }
    }

    /**
     * Return the bootstrappers array.
     *
     * @return array
     */
    protected function bootstrappers()
    {
        return $this->bootstrappers;
    }

    /**
     * Call the terminate method on any terminable middleware.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param Response                                  $response
     */
    public function terminate($request, $response)
    {
        $this->terminateMiddleware($request, $response);
        $this->app->terminate();
    }

    /**
     * Call the terminate method on any terminable middleware.
     *
     * @param \Illuminate\Http\Request  $request
     * @param \Illuminate\Http\Response $response
     */
    protected function terminateMiddleware($request, $response)
    {
        $middlewares = $this->app->shouldSkipMiddleware() ? [] : array_merge(
            $this->gatherRouteMiddleware($request),
            $this->middleware
        );

        foreach ($middlewares as $middleware) {
            if (! is_string($middleware)) {
                continue;
            }

            list($name) = $this->parseMiddleware($middleware);

            $instance = $this->app->make($name);

            if (method_exists($instance, 'terminate')) {
                $instance->terminate($request, $response);
            }
        }
    }

    /**
     * Gather the route middleware for the given request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    protected function gatherRouteMiddleware($request)
    {
        if ($route = $request->route()) {
            return $this->router->gatherRouteMiddleware($route);
        }

        return [];
    }

    /**
     * Parse a middleware string to get the name and parameters.
     *
     * @param string $middleware
     *
     * @return array
     */
    protected function parseMiddleware($middleware)
    {
        list($name, $parameters) = array_pad(explode(':', $middleware, 2), 2, []);

        if (is_string($parameters)) {
            $parameters = explode(',', $parameters);
        }

        return [$name, $parameters];
    }

    /**
     * Add a new middleware to beginning of the stack if it does not already exist.
     *
     * @param string $middleware
     *
     * @return $this
     */
    public function prependMiddleware($middleware)
    {
        if (array_search($middleware, $this->middleware) === false) {
            array_unshift($this->middleware, $middleware);
        }

        return $this;
    }

    /**
     * Add a new middleware to end of the stack if it does not already exist.
     *
     * @param string $middleware
     *
     * @return $this
     */
    public function pushMiddleware($middleware)
    {
        if (array_search($middleware, $this->middleware) === false) {
            $this->middleware[] = $middleware;
        }

        return $this;
    }

    /**
     * Return the application instance.
     *
     * @return Application
     */
    public function getApplication()
    {
        return $this->app;
    }

    /**
     * Get the application's route middleware groups.
     *
     * @return array
     */
    public function getMiddlewareGroups()
    {
        return $this->middlewareGroups;
    }

    /**
     * Get the application's route middleware.
     *
     * @return array
     */
    public function getRouteMiddleware()
    {
        return $this->routeMiddleware;
    }

    /**
     * Report the exception to the exception handler.
     *
     * @param Exception $e
     */
    protected function reportException(Exception $e)
    {
        $this->app[Handler::class]->report($e);
    }

    /**
     * Render the exception to a response.
     *
     * @param \Illuminate\Http\Request $request
     * @param Exception                $e
     *
     * @return Response
     */
    protected function renderException($request, Exception $e)
    {
        return $this->app[Handler::class]->render($request, $e);
    }
}
