<?php

use Illuminate\Container\Container;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Log\LogManager;
use Illuminate\Routing\Redirector;
use Psr\Log\LoggerInterface;

if (! function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @param string $abstract
     * @param array  $parameters
     *
     * @return mixed|\Themosis\Core\Application
     */
    function app($abstract = null, array $parameters = [])
    {
        if (is_null($abstract)) {
            return Container::getInstance();
        }

        return Container::getInstance()->make($abstract, $parameters);
    }
}

if (! function_exists('app_path')) {
    /**
     * Get the application path.
     *
     * @param string $path
     *
     * @return string
     */
    function app_path($path = '')
    {
        return app()->path($path);
    }
}

if (! function_exists('base_path')) {
    /**
     * Get the path to the base of the install.
     *
     * @param string $path
     *
     * @return string
     */
    function base_path($path = '')
    {
        return app()->basePath($path);
    }
}

if (! function_exists('config')) {
    /**
     * Get / set the specified configuration value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param array|string $key
     * @param mixed        $default
     *
     * @throws \Illuminate\Container\EntryNotFoundException
     *
     * @return mixed|\Illuminate\Config\Repository
     */
    function config($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('config');
        }

        if (is_array($key)) {
            return app('config')->set($key);
        }

        return app('config')->get($key, $default);
    }
}

if (! function_exists('content_path')) {
    /**
     * Get the path to the content public directory.
     *
     * @param string $path
     *
     * @return string
     */
    function content_path($path = '')
    {
        return app()->contentPath($path);
    }
}

if (! function_exists('database_path')) {
    /**
     * Get the database path.
     *
     * @param string $path
     *
     * @return string
     */
    function database_path($path = '')
    {
        return app()->databasePath($path);
    }
}

if (! function_exists('logger')) {
    /**
     * Log a debug message to the logs.
     *
     * @param string $message
     * @param array  $context
     *
     * @return LogManager
     */
    function logger($message = null, array $context = [])
    {
        if (is_null($message)) {
            return app('log');
        }

        return app('log')->debug($message, $context);
    }
}

if (! function_exists('logs')) {
    /**
     * Get a log driver instance.
     *
     * @param string $driver
     *
     * @return LogManager|LoggerInterface
     */
    function logs($driver = null)
    {
        return $driver ? app('log')->driver($driver) : app('log');
    }
}

if (! function_exists('redirect')) {
    /**
     * Get a redirector instance.
     *
     * @param null  $to
     * @param int   $status
     * @param array $headers
     * @param null  $secure
     *
     * @return Redirector|RedirectResponse
     */
    function redirect($to = null, $status = 302, $headers = [], $secure = null)
    {
        if (is_null($to)) {
            return app('redirect');
        }

        return app('redirect')->to($to, $status, $headers, $secure);
    }
}

if (! function_exists('request')) {
    /**
     * Get an instance of the current request or an input item from the request.
     *
     * @param array|string $key
     * @param mixed        $default
     *
     * @return Request|string|array
     */
    function request($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('request');
        }

        if (is_array($key)) {
            return app('request')->only($key);
        }

        $value = app('request')->__get($key);

        return is_null($value) ? value($default) : $value;
    }
}

if (! function_exists('resource_path')) {
    /**
     * Get the path to the resources folder.
     *
     * @param string $path
     *
     * @return string
     */
    function resource_path($path = '')
    {
        return app()->resourcePath($path);
    }
}

if (! function_exists('response')) {
    /**
     * Return a new response from the application.
     *
     * @param string $content
     * @param int    $status
     * @param array  $headers
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    function response($content = '', $status = 200, array $headers = [])
    {
        $factory = app(ResponseFactory::class);

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($content, $status, $headers);
    }
}

if (! function_exists('route')) {
    /**
     * Generate the URL to a named route.
     *
     * @param $name
     * @param array $parameters
     * @param bool  $absolute
     *
     * @return string
     */
    function route($name, $parameters = [], $absolute = true)
    {
        return app('url')->route($name, $parameters, $absolute);
    }
}

if (! function_exists('storage_path')) {
    /**
     * Get the path to the storage folder.
     *
     * @param string $path
     *
     * @return string
     */
    function storage_path($path = '')
    {
        return app()->storagePath($path);
    }
}

if (! function_exists('themes_path')) {
    /**
     * Get the path to the themes folder.
     *
     * @param string $path
     *
     * @return string
     */
    function themes_path($path = '')
    {
        return app()->themesPath($path);
    }
}

if (! function_exists('url')) {
    /**
     * Return a URL for the application.
     *
     * @param string $path
     * @param array  $parameters
     * @param bool   $secure
     *
     * @return UrlGenerator|string
     */
    function url($path = null, $parameters = [], $secure = null)
    {
        if (is_null($path)) {
            return app(UrlGenerator::class);
        }

        return app(UrlGenerator::class)->to($path, $parameters, $secure);
    }
}

if (! function_exists('validator')) {
    /**
     * Create a new validator instance.
     *
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     *
     * @return ValidationFactory|\Illuminate\Validation\Validator
     */
    function validator(array $data = [], array $rules = [], array $messages = [], array $customAttributes = [])
    {
        /** @var ValidationFactory $factory */
        $factory = app(ValidationFactory::class);

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($data, $rules, $messages, $customAttributes);
    }
}

if (! function_exists('view')) {
    /**
     * Get the evaluated view contents for the given view.
     *
     * @param string $view
     * @param array  $data
     * @param array  $mergeData
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    function view($view = null, $data = [], $mergeData = [])
    {
        $factory = app(ViewFactory::class);

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($view, $data, $mergeData);
    }
}

if (! function_exists('web_path')) {
    /**
     * Get the public web path.
     *
     * @param string $path
     *
     * @return string
     */
    function web_path($path = '')
    {
        return app()->webPath($path);
    }
}
