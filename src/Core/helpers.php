<?php

use Illuminate\Container\Container;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Log\LogManager;
use Illuminate\Routing\Redirector;
use Illuminate\Support\HtmlString;
use Psr\Log\LoggerInterface;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Themosis\Core\Mix;

if (! function_exists('abort')) {
    /**
     * Throw an HttpException with the given data.
     *
     * @param Response|Responsable|int $code
     * @param string                   $message
     * @param array                    $headers
     *
     * @throws HttpResponseException
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    function abort($code, $message = '', array $headers = [])
    {
        if ($code instanceof Response) {
            throw new HttpResponseException($code);
        } elseif ($code instanceof Responsable) {
            throw new HttpResponseException($code->toResponse(\request()));
        }

        app()->abort($code, $message, $headers);
    }
}

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

if (! function_exists('asset')) {
    /**
     * Generate an asset path for the application.
     *
     * @param string $path
     * @param bool   $secure
     *
     * @return string
     */
    function asset($path, $secure = null)
    {
        return app('url')->asset($path, $secure);
    }
}

if (! function_exists('auth')) {
    /**
     * Get the available auth instance.
     *
     * @param string|null $guard
     *
     * @return \Illuminate\Contracts\Auth\Factory|\Illuminate\Contracts\Auth\Guard
     */
    function auth($guard = null)
    {
        if (is_null($guard)) {
            return app(AuthFactory::class);
        }

        return app(AuthFactory::class)->guard($guard);
    }
}

if (! function_exists('back')) {
    /**
     * Create a redirect response to the previous location.
     *
     * @param int   $status
     * @param array $headers
     * @param mixed $fallback
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    function back($status = 302, $headers = [], $fallback = false)
    {
        return app('redirect')->back($status, $headers, $fallback);
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

if (! function_exists('public_path')) {
    /**
     * Get the path to the public folder.
     *
     * @param string $path
     *
     * @return string
     */
    function public_path($path = '')
    {
        return app()->make('path.public').($path ? DIRECTORY_SEPARATOR.ltrim($path, DIRECTORY_SEPARATOR) : $path);
    }
}

if (! function_exists('cache')) {
    /**
     * Get / set the specified cache value.
     *
     * If an array is passed, we'll assume you want to put to the cache.
     *
     * @param  dynamic  key|key,default|data,expiration|null
     *
     * @throws \Exception
     *
     * @return mixed|\Illuminate\Cache\CacheManager
     */
    function cache()
    {
        $arguments = func_get_args();

        if (empty($arguments)) {
            return app('cache');
        }

        if (is_string($arguments[0])) {
            return app('cache')->get(...$arguments);
        }

        if (! is_array($arguments[0])) {
            throw new Exception(
                'When setting a value in the cache, you must pass an array of key / value pairs.'
            );
        }

        if (! isset($arguments[1])) {
            throw new Exception(
                'You must specify an expiration time when setting a value in the cache.'
            );
        }

        return app('cache')->put(key($arguments[0]), reset($arguments[0]), $arguments[1]);
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

if (! function_exists('config_path')) {
    /**
     * Return the root config path.
     *
     * @param string $path
     *
     * @return string
     */
    function config_path($path = '')
    {
        return app()->configPath($path);
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

if (! function_exists('csrf_field')) {
    /**
     * Generate a CSRF token form field.
     *
     * @return HtmlString
     */
    function csrf_field()
    {
        return new HtmlString('<input type="hidden" name="_token" value="'.csrf_token().'">');
    }
}

if (! function_exists('csrf_token')) {
    /**
     * Get the CSRF token value.
     *
     * @return string
     */
    function csrf_token()
    {
        $session = app('session');

        if (isset($session)) {
            return $session->token();
        }

        throw new RuntimeException('Application session store not set.');
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

if (! function_exists('decrypt')) {
    /**
     * Decrypt the given value.
     *
     * @param string $value
     * @param bool   $unserialize
     *
     * @return mixed
     */
    function decrypt($value, $unserialize = true)
    {
        return app('encrypter')->decrypt($value, $unserialize);
    }
}

if (! function_exists('dummy_path')) {
    /**
     * Get dummy path.
     *
     * @param string $path
     *
     * @return string
     */
    function dummy_path($path = '')
    {
        return '';
    }
}

if (! function_exists('encrypt')) {
    /**
     * Encrypt the given value.
     *
     * @param mixed $value
     * @param bool  $serialize
     *
     * @return string
     */
    function encrypt($value, $serialize = true)
    {
        return app('encrypter')->encrypt($value, $serialize);
    }
}

if (! function_exists('event')) {
    /**
     * Dispatch an event and call the listeners.
     *
     * @param string|object $args
     *
     * @return array|null
     */
    function event(...$args)
    {
        return app('events')->dispatch(...$args);
    }
}

if (! function_exists('is_subpage')) {
    /**
     * Determine if current WordPress condition is a sub-page (of).
     *
     * @param int|string|array $parent
     *
     * @return bool
     */
    function is_subpage($parent)
    {
        global $post;

        if (is_null($post)) {
            return false;
        }

        if (empty($parent)) {
            if (is_page() && $post->post_parent > 0) {
                return true;
            }
        } else {
            $parentPost = get_post($post->post_parent);

            if (is_numeric($parent) && is_page() && (int) $parent == $post->post_parent) {
                return true;
            } elseif (is_string($parent) && is_page()) {
                if (is_a($parentPost, 'WP_Post') && $parent === $parentPost->post_name) {
                    return true;
                }
            } elseif (is_array($parent) && is_page()) {
                if (in_array($parentPost->ID, $parent, true) || in_array($parentPost->post_name, $parent, true)) {
                    return true;
                }
            }
        }

        return false;
    }
}

if (! function_exists('load_application_textdomain')) {
    /**
     * Register the .mo file for the application text domain translations.
     *
     * @param string $domain
     * @param string $locale
     *
     * @throws ErrorException
     *
     * @return bool
     */
    function load_application_textdomain(string $domain, string $locale)
    {
        if (! function_exists('load_textdomain')) {
            throw new ErrorException(
                'Function called too early. Function depends on the {load_textdomain} WordPress function.'
            );
        }

        $path = resource_path('languages'.DS.$locale.DS.$domain.'.mo');

        if (file_exists($path) && is_readable($path)) {
            return load_textdomain($domain, $path);
        }

        return false;
    }
}

if (! function_exists('load_themosis_plugin_textdomain')) {
    /**
     * Register the .mo file for any themosis plugins. Work for extensions
     * installed inside the "plugins" and "mu-plugins" directories.
     *
     * @param string $domain
     * @param string $path
     *
     * @return bool
     */
    function load_themosis_plugin_textdomain(string $domain, string $path)
    {
        /**
         * Filters a plugin's locale.
         *
         * @since 3.0.0
         *
         * @param string $locale The plugin's current locale.
         * @param string $domain Text domain. Unique identifier for retrieving translated strings.
         */
        $locale = apply_filters('plugin_locale', determine_locale(), $domain);

        $mofile = $domain.'-'.$locale.'.mo';

        return load_textdomain($domain, rtrim($path, '\/').DIRECTORY_SEPARATOR.$mofile);
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

if (! function_exists('meta')) {
    /**
     * Retrieve metadata for the specified object.
     *
     * @param int    $object_id
     * @param string $meta_key
     * @param bool   $single
     * @param string $meta_type
     *
     * @return mixed
     */
    function meta($object_id, $meta_key = '', $single = false, $meta_type = 'post')
    {
        return get_metadata($meta_type, $object_id, $meta_key, $single);
    }
}
if (! function_exists('method_field')) {
    /**
     * Generate a form field to spoof the HTTP verb usef by forms.
     *
     * @param string $method
     *
     * @return HtmlString
     */
    function method_field($method)
    {
        return new HtmlString('<input type="hidden" name="_method" value="'.$method.'">');
    }
}

if (! function_exists('mix')) {
    /**
     * Get the path to a versioned Mix file.
     *
     * @param string $path
     * @param string $manifestDirectory
     *
     * @throws \Exception
     *
     * @return \Illuminate\Support\HtmlString|string
     */
    function mix($path, $manifestDirectory = '')
    {
        return app(Mix::class)(...func_get_args());
    }
}

if (! function_exists('muplugins_path')) {
    /**
     * Return the mu-plugins path.
     *
     * @param string $path
     *
     * @return string
     */
    function muplugins_path($path = '')
    {
        return app()->mupluginsPath($path);
    }
}

if (! function_exists('plugins_path')) {
    /**
     * Return the plugins path.
     *
     * @param string $path
     *
     * @return string
     */
    function plugins_path($path = '')
    {
        return app()->pluginsPath($path);
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

if (! function_exists('report')) {
    /**
     * Report an exception.
     *
     * @param \Exception $exception
     */
    function report($exception)
    {
        if ($exception instanceof Throwable &&
            ! $exception instanceof Exception) {
            $exception = new FatalThrowableError($exception);
        }
        app(ExceptionHandler::class)->report($exception);
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
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
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

if (! function_exists('rootUrl')) {
    /**
     * Return an URL based on the root domain.
     *
     * @param string $uri
     *
     * @return string
     */
    function rootUrl(string $uri = ''): string
    {
        $request = app('request');
        // ensure one slash on left, none on the right
        $uri = ($uri) ? '/'.trim($uri, '/') : $uri;

        return $request->getSchemeAndHttpHost().$uri;
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
        $path = app('url')->route($name, $parameters, false);

        return ($absolute) ? rootUrl($path) : $path;
    }
}

if (! function_exists('secure_asset')) {
    /**
     * Generate an asset path for the application.
     *
     * @param string $path
     *
     * @return string
     */
    function secure_asset($path)
    {
        return asset($path, true);
    }
}

if (! function_exists('session')) {
    /**
     * Get / set the specified session value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param array|string $key
     * @param mixed        $default
     *
     * @throws \Illuminate\Container\EntryNotFoundException
     *
     * @return mixed|\Illuminate\Session\Store|\Illuminate\Session\SessionManager
     */
    function session($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('session');
        }

        if (is_array($key)) {
            return app('session')->put($key);
        }

        return app('session')->get($key, $default);
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

if (! function_exists('trans')) {
    /**
     * Translate the given message.
     *
     * @param string $key
     * @param array  $replace
     * @param string $locale
     *
     * @return \Illuminate\Contracts\Translation\Translator|string|array|null
     */
    function trans($key = null, $replace = [], $locale = null)
    {
        if (is_null($key)) {
            return app('translator');
        }

        return app('translator')->trans($key, $replace, $locale);
    }
}

if (! function_exists('trans_choice')) {
    /**
     * Translates the given message based on a count.
     *
     * @param string               $key
     * @param int|array|\Countable $number
     * @param array                $replace
     * @param string               $locale
     *
     * @return string
     */
    function trans_choice($key, $number, array $replace = [], $locale = null)
    {
        return app('translator')->transChoice($key, $number, $replace, $locale);
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
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\View\Factory
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
