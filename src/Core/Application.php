<?php

namespace Themosis\Core;

use Closure;
use Composer\Autoload\ClassLoader;
use Illuminate\Container\Container;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Contracts\Http\Kernel as HttpKernelContract;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Log\LogServiceProvider;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Themosis\Core\Bootstrap\EnvironmentLoader;
use Themosis\Core\Events\LocaleUpdated;
use Themosis\Route\RouteServiceProvider;

class Application extends Container implements ApplicationContract, HttpKernelInterface
{
    /**
     * Application version.
     *
     * @var string
     */
    const VERSION = '2.1.0';

    /**
     * Application textdomain.
     */
    const TEXTDOMAIN = 'themosis';

    /**
     * Base path of the framework.
     *
     * @var string
     */
    protected $basePath;

    /**
     * Path location (directory) of env files.
     *
     * @var string
     */
    protected $environmentPath;

    /**
     * Environment file name base.
     *
     * @var string
     */
    protected $environmentFile = '.env';

    /**
     * The deferred services and their providers.
     *
     * @var array
     */
    protected $deferredServices = [];

    /**
     * All of the registered service providers.
     *
     * @var array
     */
    protected $serviceProviders = [];

    /**
     * The names of the loaded service providers.
     *
     * @var array
     */
    protected $loadedProviders = [];

    /**
     * Indicates if the application has been bootstrapped or not.
     *
     * @var bool
     */
    protected $hasBeenBootstrapped = false;

    /**
     * Indicates if the application has booted.
     *
     * @var bool
     */
    protected $booted = false;

    /**
     * List of booting callbacks.
     *
     * @var array
     */
    protected $bootingCallbacks = [];

    /**
     * List of booted callbacks.
     *
     * @var array
     */
    protected $bootedCallbacks = [];

    /**
     * List of terminating callbacks.
     *
     * @var array
     */
    protected $terminatingCallbacks = [];

    /**
     * @var string
     */
    protected $namespace;

    public function __construct($basePath = null)
    {
        if ($basePath) {
            $this->setBasePath($basePath);
        }

        $this->registerBaseBindings();
        $this->registerBaseServiceProviders();
        $this->registerCoreContainerAliases();
    }

    /**
     * Get the version number of the application.
     *
     * @return string
     */
    public function version()
    {
        return static::VERSION;
    }

    /**
     * Register basic bindings into the container.
     */
    protected function registerBaseBindings()
    {
        static::setInstance($this);
        $this->instance('app', $this);
        $this->instance(Container::class, $this);
        $this->instance(PackageManifest::class, new PackageManifest(
            new Filesystem(),
            $this->basePath(),
            $this->getCachedPackagesPath()
        ));
    }

    /**
     * Register base service providers.
     */
    protected function registerBaseServiceProviders()
    {
        $this->register(new EventServiceProvider($this));
        $this->register(new LogServiceProvider($this));
        $this->register(new RouteServiceProvider($this));
    }

    /**
     * Register the core class aliases in the container.
     */
    protected function registerCoreContainerAliases()
    {
        $list = [
            'action' => [
                \Themosis\Hook\ActionBuilder::class
            ],
            'ajax' => [
                \Themosis\Ajax\Ajax::class
            ],
            'app' => [
                Application::class,
                \Illuminate\Contracts\Container\Container::class,
                \Illuminate\Contracts\Foundation\Application::class,
                \Psr\Container\ContainerInterface::class
            ],
            'asset' => [
                \Themosis\Asset\Factory::class,
            ],
            'auth' => [
                \Illuminate\Auth\AuthManager::class,
                \Illuminate\Contracts\Auth\Factory::class
            ],
            'auth.driver' => [
                \Illuminate\Contracts\Auth\Guard::class
            ],
            'auth.password' => [
                \Illuminate\Auth\Passwords\PasswordBrokerManager::class,
                \Illuminate\Contracts\Auth\PasswordBrokerFactory::class
            ],
            'auth.password.broker' => [
                \Illuminate\Auth\Passwords\PasswordBroker::class,
                \Illuminate\Contracts\Auth\PasswordBroker::class
            ],
            'blade.compiler' => [
                \Illuminate\View\Compilers\BladeCompiler::class
            ],
            'cache' => [
                \Illuminate\Cache\CacheManager::class,
                \Illuminate\Contracts\Cache\Factory::class
            ],
            'cache.store' => [
                \Illuminate\Cache\Repository::class,
                \Illuminate\Contracts\Cache\Repository::class
            ],
            'config' => [
                \Illuminate\Config\Repository::class,
                \Illuminate\Contracts\Config\Repository::class
            ],
            'cookie' => [
                \Illuminate\Cookie\CookieJar::class,
                \Illuminate\Contracts\Cookie\Factory::class,
                \Illuminate\Contracts\Cookie\QueueingFactory::class
            ],
            'db' => [
                \Illuminate\Database\DatabaseManager::class
            ],
            'db.connection' => [
                \Illuminate\Database\Connection::class,
                \Illuminate\Database\ConnectionInterface::class
            ],
            'encrypter' => [
                \Illuminate\Encryption\Encrypter::class,
                \Illuminate\Contracts\Encryption\Encrypter::class
            ],
            'events' => [
                \Illuminate\Events\Dispatcher::class,
                \Illuminate\Contracts\Events\Dispatcher::class
            ],
            'files' => [
                \Illuminate\Filesystem\Filesystem::class
            ],
            'filesystem' => [
                \Illuminate\Filesystem\FilesystemManager::class,
                \Illuminate\Contracts\Filesystem\Factory::class
            ],
            'filesystem.disk' => [
                \Illuminate\Contracts\Filesystem\Filesystem::class
            ],
            'filesystem.cloud' => [
                \Illuminate\Contracts\Filesystem\Cloud::class
            ],
            'filter' => [
                \Themosis\Hook\FilterBuilder::class
            ],
            'form' => [
                \Themosis\Forms\FormFactory::class
            ],
            'hash' => [
                \Illuminate\Hashing\HashManager::class
            ],
            'hash.driver' => [
                \Illuminate\Contracts\Hashing\Hasher::class
            ],
            'html' => [
                \Themosis\Html\HtmlBuilder::class
            ],
            'log' => [
                \Illuminate\Log\LogManager::class,
                \Psr\Log\LoggerInterface::class
            ],
            'mailer' => [
                \Illuminate\Mail\Mailer::class,
                \Illuminate\Contracts\Mail\Mailer::class,
                \Illuminate\Contracts\Mail\MailQueue::class
            ],
            'metabox' => [
                \Themosis\Metabox\Factory::class
            ],
            'posttype' => [
                \Themosis\PostType\Factory::class
            ],
            'redirect' => [
                \Illuminate\Routing\Redirector::class
            ],
            'redis' => [
                \Illuminate\Redis\RedisManager::class,
                \Illuminate\Contracts\Redis\Factory::class
            ],
            'request' => [
                \Illuminate\Http\Request::class,
                \Symfony\Component\HttpFoundation\Request::class
            ],
            'router' => [
                \Themosis\Route\Router::class,
                \Illuminate\Routing\Router::class,
                \Illuminate\Contracts\Routing\Registrar::class,
                \Illuminate\Contracts\Routing\BindingRegistrar::class
            ],
            'session' => [
                \Illuminate\Session\SessionManager::class
            ],
            'session.store' => [
                \Illuminate\Session\Store::class,
                \Illuminate\Contracts\Session\Session::class
            ],
            'taxonomy' => [
                \Themosis\Taxonomy\Factory::class
            ],
            'taxonomy.field' => [
                \Themosis\Taxonomy\TaxonomyFieldFactory::class
            ],
            'translator' => [
                \Illuminate\Translation\Translator::class,
                \Illuminate\Contracts\Translation\Translator::class
            ],
            'twig' => [
                \Twig_Environment::class
            ],
            'url' => [
                \Illuminate\Routing\UrlGenerator::class,
                \Illuminate\Contracts\Routing\UrlGenerator::class
            ],
            'validator' => [
                \Illuminate\Validation\Factory::class,
                \Illuminate\Contracts\Validation\Factory::class
            ],
            'view' => [
                \Illuminate\View\Factory::class,
                \Illuminate\Contracts\View\Factory::class
            ],
        ];

        foreach ($list as $key => $aliases) {
            foreach ($aliases as $alias) {
                $this->alias($key, $alias);
            }
        }
    }

    /**
     * Get the base path of the Laravel installation.
     *
     * @param string $path Optional path to append to the base path.
     *
     * @return string
     */
    public function basePath($path = '')
    {
        return $this->basePath.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Set the base path for the application.
     *
     * @param string $basePath
     *
     * @return $this
     */
    public function setBasePath($basePath)
    {
        $this->basePath = rtrim($basePath, '\/');
        $this->bindPathsInContainer();

        return $this;
    }

    /**
     * Bind all of the application paths in the container.
     */
    protected function bindPathsInContainer()
    {
        // Core
        $this->instance('path', $this->path());
        // Base
        $this->instance('path.base', $this->basePath());
        // Content
        $this->instance('path.content', $this->contentPath());
        // Mu-plugins
        $this->instance('path.muplugins', $this->mupluginsPath());
        // Plugins
        $this->instance('path.plugins', $this->pluginsPath());
        // Themes
        $this->instance('path.themes', $this->themesPath());
        // Application
        $this->instance('path.application', $this->applicationPath());
        // Resources
        $this->instance('path.resources', $this->resourcePath());
        // Languages
        $this->instance('path.lang', $this->langPath());
        // Web root
        $this->instance('path.web', $this->webPath());
        // Root
        $this->instance('path.root', $this->rootPath());
        // Config
        $this->instance('path.config', $this->configPath());
        // Public
        $this->instance('path.public', $this->webPath());
        // Storage
        $this->instance('path.storage', $this->storagePath());
        // Database
        $this->instance('path.database', $this->databasePath());
        // Bootstrap
        $this->instance('path.bootstrap', $this->bootstrapPath());
        // WordPress
        $this->instance('path.wp', $this->wordpressPath());
    }

    /**
     * Get the path to the application "themosis-application" directory.
     *
     * @param string $path
     *
     * @return string
     */
    public function path($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'app'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the WordPress "content" directory.
     *
     * @param string $path
     *
     * @return string
     */
    public function contentPath($path = '')
    {
        return WP_CONTENT_DIR.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the WordPress "mu-plugins" directory.
     *
     * @param string $path
     *
     * @return string
     */
    public function mupluginsPath($path = '')
    {
        return $this->contentPath('mu-plugins').($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the WordPress "plugins" directory.
     *
     * @param string $path
     *
     * @return string
     */
    public function pluginsPath($path = '')
    {
        return $this->contentPath('plugins').($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the WordPress "themes" directory.
     *
     * @param string $path
     *
     * @return string
     */
    public function themesPath($path = '')
    {
        return $this->contentPath('themes').($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the application directory.
     *
     * @param string $path
     *
     * @return string
     */
    public function applicationPath($path = '')
    {
        return $this->basePath('app').($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the resources directory path.
     *
     * @param string $path
     *
     * @return string
     */
    public function resourcePath($path = '')
    {
        return $this->basePath('resources').($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the path to the resources "languages" directory.
     *
     * @param string $path
     *
     * @return string
     */
    public function langPath($path = '')
    {
        return $this->resourcePath('languages').($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the path of the web server root.
     *
     * @param string $path
     *
     * @return string
     */
    public function webPath($path = '')
    {
        return $this->basePath(THEMOSIS_PUBLIC_DIR).($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the root path of the project.
     *
     * @param string $path
     *
     * @return string
     */
    public function rootPath($path = '')
    {
        if (defined('THEMOSIS_ROOT')) {
            return THEMOSIS_ROOT.($path ? DIRECTORY_SEPARATOR.$path : $path);
        }

        return $this->webPath($path);
    }

    /**
     * Get the main application plugin configuration directory.
     *
     * @param string $path
     *
     * @return string
     */
    public function configPath($path = '')
    {
        return $this->basePath('config').($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the storage directory path.
     *
     * @param string $path
     *
     * @return string
     */
    public function storagePath($path = '')
    {
        if (defined('THEMOSIS_ROOT')) {
            return $this->rootPath('storage').($path ? DIRECTORY_SEPARATOR.$path : $path);
        }

        return $this->contentPath('storage').($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the database directory path.
     *
     * @param string $path
     *
     * @return string
     */
    public function databasePath($path = '')
    {
        return $this->rootPath('database').($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the bootstrap directory path.
     *
     * @param string $path
     *
     * @return string
     */
    public function bootstrapPath($path = '')
    {
        return $this->rootPath('bootstrap').($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the WordPress directory path.
     *
     * @param string $path
     *
     * @throws \Illuminate\Container\EntryNotFoundException
     *
     * @return string
     */
    public function wordpressPath($path = '')
    {
        return $this->webPath(env('WP_DIR', 'cms')).($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Set the environment file to be loaded during bootstrapping.
     *
     * @param string $file
     *
     * @return $this
     */
    public function loadEnvironmentFrom($file)
    {
        $this->environmentFile = $file;

        return $this;
    }

    /**
     * Return the environment path directory.
     *
     * @return string
     */
    public function environmentPath()
    {
        return $this->environmentPath ?: $this->basePath();
    }

    /**
     * Set the directory for the environment file.
     *
     * @param string $path
     *
     * @return $this
     */
    public function useEnvironmentPath($path)
    {
        $this->environmentPath = $path;

        return $this;
    }

    /**
     * Return the environment file name base.
     *
     * @return string
     */
    public function environmentFile()
    {
        return $this->environmentFile ?: '.env';
    }

    /**
     * Return the environment file path.
     *
     * @return string
     */
    public function environmentFilePath()
    {
        return $this->environmentPath().DIRECTORY_SEPARATOR.$this->environmentFile();
    }

    /**
     * Get or check the current application environment.
     *
     * @param string|array $environments
     *
     * @return string|bool
     */
    public function environment(...$environments)
    {
        if (count($environments) > 0) {
            $patterns = is_array($environments[0]) ? $environments[0] : $environments;

            return Str::is($patterns, $this['env']);
        }

        return $this['env'];
    }

    /**
     * Detech application's current environment.
     *
     * @param Closure $callback
     *
     * @return string
     */
    public function detectEnvironment(Closure $callback)
    {
        $args = $_SERVER['argv'] ?? null;

        return $this['env'] = (new EnvironmentDetector())->detect($callback, $args);
    }

    /**
     * Determine if we are running in the console.
     *
     * @return bool
     */
    public function runningInConsole()
    {
        return php_sapi_name() == 'cli' || php_sapi_name() == 'phpdbg';
    }

    /**
     * Determine if the application is currently down for maintenance.
     *
     * @throws \Illuminate\Container\EntryNotFoundException
     *
     * @return bool
     */
    public function isDownForMaintenance()
    {
        $filePath = $this->wordpressPath('.maintenance');

        if (function_exists('wp_installing') && ! file_exists($filePath)) {
            return \wp_installing();
        }

        return file_exists($filePath);
    }

    /**
     * Register all of the configured providers.
     */
    public function registerConfiguredProviders()
    {
        $providers = Collection::make($this->config['app.providers'])
            ->partition(function ($provider) {
                return Str::startsWith($provider, 'Illuminate\\');
            });

        $providers->splice(1, 0, [$this->make(PackageManifest::class)->providers()]);

        (new ProviderRepository($this, new Filesystem(), $this->getCachedServicesPath()))
            ->load($providers->collapse()->toArray());
    }

    /**
     * Register a deferred provider and service.
     *
     * @param string      $provider
     * @param string|null $service
     */
    public function registerDeferredProvider($provider, $service = null)
    {
        if ($service) {
            unset($this->deferredServices[$service]);
        }

        $this->register($instance = new $provider($this));

        if (! $this->booted) {
            $this->booting(function () use ($instance) {
                $this->bootProvider($instance);
            });
        }
    }

    /**
     * Add an array of services to the application's deferred services.
     *
     * @param array $services
     */
    public function addDeferredServices(array $services)
    {
        $this->deferredServices = array_merge($this->deferredServices, $services);
    }

    /**
     * Get the application's deferred services.
     *
     * @return array
     */
    public function getDeferredServices()
    {
        return $this->deferredServices;
    }

    /**
     * Set the application's deferred services.
     *
     * @param array $services
     */
    public function setDeferredServices(array $services)
    {
        $this->deferredServices = $services;
    }

    /**
     * Verify if the application has been bootstrapped before.
     *
     * @return bool
     */
    public function hasBeenBootstrapped()
    {
        return $this->hasBeenBootstrapped;
    }

    /**
     * Bootstrap the application with given list of bootstrap
     * classes.
     *
     * @param array $bootstrappers
     */
    public function bootstrapWith(array $bootstrappers)
    {
        $this->hasBeenBootstrapped = true;

        foreach ($bootstrappers as $bootstrapper) {
            $this['events']->dispatch('bootstrapping: '.$bootstrapper, [$this]);

            /*
             * Instantiate each bootstrap class and call its "bootstrap" method
             * with the Application as a parameter.
             */
            $this->make($bootstrapper)->bootstrap($this);

            $this['events']->dispatch('bootstrapped: '.$bootstrapper, [$this]);
        }
    }

    /**
     * Boot the application's service providers.
     */
    public function boot()
    {
        if ($this->booted) {
            return;
        }

        /*
         * Once the application has booted we will also fire some "booted" callbacks
         * for any listeners that need to do work after this initial booting gets
         * finished. This is useful when ordering the boot-up processes we run.
         */
        $this->fireAppCallbacks($this->bootingCallbacks);

        array_walk($this->serviceProviders, function ($provider) {
            $this->bootProvider($provider);
        });

        $this->booted = true;

        $this->fireAppCallbacks($this->bootedCallbacks);
    }

    /**
     * Call the booting callbacks for the application.
     *
     * @param array $callbacks
     */
    protected function fireAppCallbacks(array $callbacks)
    {
        foreach ($callbacks as $callback) {
            call_user_func($callback, $this);
        }
    }

    /**
     * Boot the given service provider.
     *
     * @param ServiceProvider $provider
     *
     * @return mixed
     */
    protected function bootProvider(ServiceProvider $provider)
    {
        if (method_exists($provider, 'boot')) {
            return $this->call([$provider, 'boot']);
        }
    }

    /**
     * Register a new boot listener.
     *
     * @param mixed $callback
     */
    public function booting($callback)
    {
        $this->bootingCallbacks[] = $callback;
    }

    /**
     * Register a new "booted" listener.
     *
     * @param mixed $callback
     */
    public function booted($callback)
    {
        $this->bootedCallbacks[] = $callback;

        if ($this->isBooted()) {
            $this->fireAppCallbacks([$callback]);
        }
    }

    /**
     * Get the path to the cached services.php file.
     *
     * @return string
     */
    public function getCachedServicesPath()
    {
        return $this->bootstrapPath('cache/services.php');
    }

    /**
     * Get the path to the cached packages.php file.
     *
     * @return string
     */
    public function getCachedPackagesPath()
    {
        return $this->bootstrapPath('cache/packages.php');
    }

    /**
     * Determine if the application configuration is cached.
     *
     * @return bool
     */
    public function configurationIsCached()
    {
        return file_exists($this->getCachedConfigPath());
    }

    /**
     * Get the path to the configuration cache file.
     *
     * @return string
     */
    public function getCachedConfigPath()
    {
        return $this->bootstrapPath('cache/config.php');
    }

    /**
     * Handles a Request to convert it to a Response.
     *
     * When $catch is true, the implementation must catch all exceptions
     * and do its best to convert them to a Response instance.
     *
     * @param SymfonyRequest $request A Request instance
     * @param int            $type    The type of the request
     *                                (one of HttpKernelInterface::MASTER_REQUEST or HttpKernelInterface::SUB_REQUEST)
     * @param bool           $catch   Whether to catch exceptions or not
     *
     * @throws \Exception When an Exception occurs during processing
     *
     * @return Response A Response instance
     */
    public function handle(SymfonyRequest $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        return $this[HttpKernelContract::class]->handle(Request::createFromBase($request));
    }

    /**
     * Register a service provider with the application.
     *
     * @param \Illuminate\Support\ServiceProvider|string $provider
     * @param array                                      $options
     * @param bool                                       $force
     *
     * @return \Illuminate\Support\ServiceProvider
     */
    public function register($provider, $options = [], $force = false)
    {
        if (($registered = $this->getProvider($provider)) && ! $force) {
            return $registered;
        }

        // If the given "provider" is a string, we will resolve it, passing in the
        // application instance automatically for the developer. This is simply
        // a more convenient way of specifying your service provider classes.
        if (is_string($provider)) {
            $provider = $this->resolveProvider($provider);
        }

        if (method_exists($provider, 'register')) {
            $provider->register();
        }

        // If there are bindings / singletons set as properties on the provider we
        // will spin through them and register them with the application, which
        // serves as a convenience layer while registering a lot of bindings.
        if (property_exists($provider, 'bindings')) {
            foreach ($provider->bindings as $key => $value) {
                $this->bind($key, $value);
            }
        }

        if (property_exists($provider, 'singletons')) {
            foreach ($provider->singletons as $key => $value) {
                $this->singleton($key, $value);
            }
        }

        $this->markAsRegistered($provider);

        // If the application has already booted, we will call this boot method on
        // the provider class so it has an opportunity to do its boot logic and
        // will be ready for any usage by this developer's application logic.
        if ($this->booted) {
            $this->bootProvider($provider);
        }

        return $provider;
    }

    /**
     * Get the registered service provider instance if it exists.
     *
     * @param ServiceProvider|string $provider
     *
     * @return ServiceProvider|null
     */
    public function getProvider($provider)
    {
        return array_values($this->getProviders($provider))[0] ?? null;
    }

    /**
     * Get the registered service provider instances if any exist.
     *
     * @param ServiceProvider|string $provider
     *
     * @return array
     */
    public function getProviders($provider)
    {
        $name = is_string($provider) ? $provider : get_class($provider);

        return Arr::where($this->serviceProviders, function ($value) use ($name) {
            return $value instanceof $name;
        });
    }

    /**
     * Get the service providers that have been loaded.
     *
     * @return array
     */
    public function getLoadedProviders()
    {
        return $this->loadedProviders;
    }

    /**
     * Resolve a service provider instance from the class name.
     *
     * @param string $provider
     *
     * @return ServiceProvider
     */
    public function resolveProvider($provider)
    {
        return new $provider($this);
    }

    /**
     * Mark the given provider as registered.
     *
     * @param ServiceProvider $provider
     */
    protected function markAsRegistered($provider)
    {
        $this->serviceProviders[] = $provider;
        $this->loadedProviders[get_class($provider)] = true;
    }

    /**
     * Determine if the application is running unit tests.
     *
     * @return bool
     */
    public function runningUnitTests()
    {
        return $this['env'] == 'testing';
    }

    /**
     * Resolve the given type from the container.
     *
     * (Overriding Container::make)
     *
     * @param string $abstract
     * @param array  $parameters
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return mixed
     */
    public function make($abstract, array $parameters = [])
    {
        $abstract = $this->getAlias($abstract);

        if (isset($this->deferredServices[$abstract]) && ! isset($this->instances[$abstract])) {
            $this->loadDeferredProvider($abstract);
        }

        return parent::make($abstract, $parameters);
    }

    /**
     * Load and boot all of the remaining deferred providers.
     */
    public function loadDeferredProviders()
    {
        // We will simply spin through each of the deferred providers and register each
        // one and boot them if the application has booted. This should make each of
        // the remaining services available to this application for immediate use.
        foreach ($this->deferredServices as $service => $provider) {
            $this->loadDeferredProvider($service);
        }

        $this->deferredServices = [];
    }

    /**
     * Load the provider for a deferred service.
     *
     * @param string $service
     */
    public function loadDeferredProvider($service)
    {
        if (! isset($this->deferredServices[$service])) {
            return;
        }

        $provider = $this->deferredServices[$service];

        // If the service provider has not already been loaded and registered we can
        // register it with the application and remove the service from this list
        // of deferred services, since it will already be loaded on subsequent.
        if (! isset($this->loadedProviders[$provider])) {
            $this->registerDeferredProvider($provider, $service);
        }
    }

    /**
     * Determine if the given abstract type has been bound.
     *
     * (Overriding Container::bound)
     *
     * @param string $abstract
     *
     * @return bool
     */
    public function bound($abstract)
    {
        return isset($this->deferredServices[$abstract]) || parent::bound($abstract);
    }

    /**
     * Determine if the application has booted.
     *
     * @return bool
     */
    public function isBooted()
    {
        return $this->booted;
    }

    /**
     * Determine if middleware has been disabled for the application.
     *
     * @return bool
     */
    public function shouldSkipMiddleware()
    {
        return $this->bound('middleware.disable') &&
            $this->make('middleware.disable') === true;
    }

    /**
     * Bootstrap a Themosis like plugin.
     *
     * @param string $filePath
     * @param string $configPath
     *
     * @return PluginManager
     */
    public function loadPlugin(string $filePath, string $configPath)
    {
        $plugin = (new PluginManager($this, $filePath, new ClassLoader()))->load($configPath);

        $this->instance('wp.plugin.'.$plugin->getHeader('plugin_id'), $plugin);

        return $plugin;
    }

    /**
     * Register the framework core "plugin" and auto-load
     * any found mu-plugins after the framework.
     *
     * @param string $pluginsPath
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Exception
     */
    public function loadPlugins(string $pluginsPath)
    {
        $directories = Collection::make((new Filesystem())->directories($this->mupluginsPath()))
            ->map(function ($directory) {
                return ltrim(substr($directory, strrpos($directory, DS)), '\/');
            })->toArray();

        (new PluginsRepository($this, new Filesystem(), $pluginsPath, $this->getCachedPluginsPath()))
            ->load($directories);
    }

    /**
     * Register a plugin and load it.
     *
     * @param string $path Plugin relative path (pluginDirName/pluginMainFile).
     */
    public function registerPlugin(string $path)
    {
        require $this->mupluginsPath($path);
    }

    /**
     * Return cached plugins manifest file path.
     *
     * @return string
     */
    public function getCachedPluginsPath()
    {
        return $this->bootstrapPath('cache/plugins.php');
    }

    /**
     * Register a list of hookable instances.
     *
     * @param string $config
     */
    public function registerConfiguredHooks(string $config = '')
    {
        if (empty($config)) {
            $config = 'app.hooks';
        }

        $hooks = Collection::make($this->config[$config]);

        (new HooksRepository($this))->load($hooks->all());
    }

    /**
     * Create and register a hook instance.
     *
     * @param string $hook
     */
    public function registerHook(string $hook)
    {
        // Build a "Hookable" instance.
        // Hookable instances must extend the "Hookable" class.
        $instance = new $hook($this);
        $hooks = (array) $instance->hook;

        if (! method_exists($instance, 'register')) {
            return;
        }

        if (! empty($hooks)) {
            $this['action']->add($hooks, [$instance, 'register'], $instance->priority);
        } else {
            $instance->register();
        }
    }

    /**
     * Load current active theme.
     *
     * @param string $dirPath    The theme directory path.
     * @param string $configPath The theme relative configuration folder path.
     *
     * @return ThemeManager
     */
    public function loadTheme(string $dirPath, string $configPath)
    {
        $theme = (new ThemeManager($this, $dirPath, new ClassLoader()))
            ->load($dirPath.'/'.trim($configPath, '\/'));

        $this->instance('wp.theme', $theme);

        return $theme;
    }

    /**
     * Load configuration files based on given path.
     *
     * @param Repository $config
     * @param string     $path   The configuration files folder path.
     *
     * @return Application
     */
    public function loadConfigurationFiles(Repository $config, $path = '')
    {
        $files = $this->getConfigurationFiles($path);

        foreach ($files as $key => $path) {
            $config->set($key, require $path);
        }

        return $this;
    }

    /**
     * Get all configuration files.
     *
     * @param mixed $path
     *
     * @return array
     */
    protected function getConfigurationFiles($path)
    {
        $files = [];

        foreach (Finder::create()->files()->name('*.php')->in($path) as $file) {
            $directory = $this->getNestedDirectory($file, $path);
            $files[$directory.basename($file->getRealPath(), '.php')] = $file->getRealPath();
        }

        ksort($files, SORT_NATURAL);

        return $files;
    }

    /**
     * Get configuration file nesting path.
     *
     * @param SplFileInfo $file
     * @param string      $path
     *
     * @return string
     */
    protected function getNestedDirectory(SplFileInfo $file, $path)
    {
        $directory = $file->getPath();

        if ($nested = trim(str_replace($path, '', $directory), DIRECTORY_SEPARATOR)) {
            $nested = str_replace(DIRECTORY_SEPARATOR, '.', $nested).'.';
        }

        return $nested;
    }

    /**
     * Throw an HttpException with the given data.
     *
     * @param int    $code
     * @param string $message
     * @param array  $headers
     */
    public function abort($code, $message = '', array $headers = [])
    {
        if (404 == $code) {
            throw new NotFoundHttpException($message);
        }

        throw new HttpException($code, $message, null, $headers);
    }

    /**
     * Register a terminating callback with the application.
     *
     * @param Closure $callback
     *
     * @return $this
     */
    public function terminating(Closure $callback)
    {
        $this->terminatingCallbacks[] = $callback;

        return $this;
    }

    /**
     * Terminate the application.
     */
    public function terminate()
    {
        foreach ($this->terminatingCallbacks as $terminating) {
            $this->call($terminating);
        }
    }

    /**
     * Handle incoming request and return a response.
     * Abstract the implementation from the user for easy
     * theme integration.
     *
     * @param string                                    $kernel  Application kernel class name.
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return $this
     */
    public function manage(string $kernel, $request)
    {
        $kernel = $this->make($kernel);

        $response = $kernel->handle($request);
        $response->send();

        $kernel->terminate($request, $response);

        return $this;
    }

    /**
     * Handle WordPress administration incoming request.
     * Only send response headers.
     *
     * @param string                                    $kernel
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return $this;
     */
    public function manageAdmin(string $kernel, $request)
    {
        if (! $this->isWordPressAdmin() && ! $this->has('action')) {
            return $this;
        }

        $this['action']->add('admin_init', $this->dispatchToAdmin($kernel, $request));

        return $this;
    }

    /**
     * Manage WordPress Admin Init.
     * Handle incoming request and return a response.
     *
     * @param string $kernel
     * @param $request
     *
     * @return Closure
     */
    protected function dispatchToAdmin(string $kernel, $request)
    {
        return function () use ($kernel, $request) {
            $kernel = $this->make($kernel);

            /** @var Response $response */
            $response = $kernel->handle($request);

            if (500 <= $response->getStatusCode()) {
                // In case of an internal server error, we stop the process
                // and send full response back to the user.
                $response->send();
            } else {
                // HTTP OK - Send only the response headers.s
                $response->sendHeaders();
            }
        };
    }

    /**
     * Register a callback to run after loading the environment.
     *
     * @param Closure $callback
     */
    public function afterLoadingEnvironment(Closure $callback)
    {
        $this->afterBootstrapping(EnvironmentLoader::class, $callback);
    }

    /**
     * Register a callback to run before a bootstrapper.
     *
     * @param string  $bootstrapper
     * @param Closure $callback
     */
    public function beforeBootstrapping($bootstrapper, Closure $callback)
    {
        $this['events']->listen('bootstrapping: '.$bootstrapper, $callback);
    }

    /**
     * Register a callback to run after a bootstrapper.
     *
     * @param string  $bootstrapper
     * @param Closure $callback
     */
    public function afterBootstrapping($bootstrapper, Closure $callback)
    {
        $this['events']->listen('bootstrapped: '.$bootstrapper, $callback);
    }

    /**
     * Set the application locale.
     *
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this['config']->set('app.locale', $locale);
        $this['translator']->setLocale($locale);
        $this['events']->dispatch(new LocaleUpdated($locale));
    }

    /**
     * Get the application locale.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this['config']->get('app.locale');
    }

    /**
     * Check if passed locale is current locale.
     *
     * @param string $locale
     *
     * @return bool
     */
    public function isLocale($locale)
    {
        return $this->getLocale() == $locale;
    }

    /**
     * Return the application namespace.
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function getNamespace()
    {
        if (! is_null($this->namespace)) {
            return $this->namespace;
        }

        $composer = json_decode(file_get_contents(base_path('composer.json')), true);

        foreach ((array) data_get($composer, 'autoload.psr-4') as $namespace => $path) {
            foreach ((array) $path as $pathChoice) {
                if (realpath(app_path()) == realpath(base_path($pathChoice))) {
                    return $this->namespace = $namespace;
                }
            }
        }

        throw new \RuntimeException('Unable to detect application namespace.');
    }

    /**
     * Determine if the application routes are cached.
     *
     * @return bool
     */
    public function routesAreCached()
    {
        return $this['files']->exists($this->getCachedRoutesPath());
    }

    /**
     * Get the path to the routes cache file.
     *
     * @return string
     */
    public function getCachedRoutesPath()
    {
        return $this->bootstrapPath('cache/routes.php');
    }

    /**
     * Determine if we currently inside the WordPress administration.
     *
     * @return bool
     */
    public function isWordPressAdmin()
    {
        if (isset($GLOBALS['current_screen']) && is_a($GLOBALS['current_screen'], 'WP_Screen')) {
            return $GLOBALS['current_screen']->in_admin();
        } elseif (defined('WP_ADMIN')) {
            return WP_ADMIN;
        }

        return false;
    }

    /**
     * Return a Javascript Global variable.
     *
     * @param string $name
     * @param array  $data
     *
     * @return string
     */
    public function outputJavascriptGlobal(string $name, array $data)
    {
        $output = "<script type=\"text/javascript\">\n\r";
        $output .= "/* <![CDATA[ */\n\r";
        $output .= "var {$name} = {\n\r";

        if (! empty($data) && is_array($data)) {
            foreach ($data as $key => $value) {
                $output .= $key.': '.json_encode($value).",\n\r";
            }
        }

        $output .= "};\n\r";
        $output .= "/* ]]> */\n\r";
        $output .= '</script>';

        return $output;
    }
}
