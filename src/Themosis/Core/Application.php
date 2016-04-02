<?php
namespace Themosis\Core;

use Themosis\Action\Action;
use Themosis\Facades\Facade;
use Themosis\Route\RouteIgniterService;

class Application extends Container {

    /**
     * All of the registered service igniters.
     *
     * @var array
     */
    protected $serviceIgniters = [];

    /**
     * The names of the loaded service igniters.
     *
     * @var array
     */
    protected $loadedIgniters = [];

    /**
     * The deferred services and their igniters.
     *
     * @var array
     */
    protected $deferredServices = [];

    /**
     * The request class name.
     *
     * @var string
     */
    protected static $requestClass = 'Themosis\Core\Request';

    /**
     * Build an Application instance.
     *
     * @param \Themosis\Core\Request $request
     */
    public function __construct(Request $request = null)
    {
        $this->registerBaseBindings($request ?: $this->createNewRequest());

        $this->registerBaseServiceIgniters();

        // Listen to front-end request.
        Action::listen('themosis_run', $this, 'run')->dispatch();
    }

    /**
     * Create a new Request instance.
     *
     * @return \Themosis\Core\Request
     */
    protected function createNewRequest()
    {
        return forward_static_call([static::$requestClass, 'createFromGlobals']);
    }

    /**
     * Register base framework classes into the container.
     *
     * @param \Themosis\Core\Request $request
     * @return void
     */
    protected function registerBaseBindings(Request $request)
    {
        $this->instance('request', $request);
        $this->instance('Themosis\Core\Container', $this);
    }

    /**
     * Register all of the base service providers.
     *
     * @return void
     */
    protected function registerBaseServiceIgniters()
    {
        $this->registerRouteIgniter();
    }

    /**
     * Register the routing service provider.
     *
     * @return void
     */
    protected function registerRouteIgniter()
    {
        $this->register(new RouteIgniterService($this));
    }

    /**
     * Register all igniter services classes.
     *
     * @return void
     */
    public function registerCoreIgniters()
    {
        $services = [
            '\Themosis\Action\ActionIgniterService',
            '\Themosis\Ajax\AjaxIgniterService',
            '\Themosis\Asset\AssetIgniterService',
            '\Themosis\Configuration\ConfigIgniterService',
            '\Themosis\Field\FieldIgniterService',
            '\Themosis\Html\FormIgniterService',
            '\Themosis\Html\HtmlIgniterService',
            '\Themosis\Metabox\MetaboxIgniterService',
            '\Themosis\Page\PageIgniterService',
            '\Themosis\PostType\PostTypeIgniterService',
            '\Themosis\Route\RouteIgniterService',
            '\Themosis\Page\Sections\SectionIgniterService',
            '\Themosis\Taxonomy\TaxonomyIgniterService',
            '\Themosis\User\UserIgniterService',
            '\Themosis\Validation\ValidationIgniterService',
            '\Themosis\View\ViewIgniterService'
        ];

        foreach ($services as $service)
        {
            /**
             * Register the igniterService instance.
             * The facade will call the appropriate igniterService.
             */
            $this->register($service);
        }
    }

    /**
     * Register all IgniterServices and set their instances.
     *
     * @param \Themosis\Core\IgniterService|string $igniter The IgniterService class name or the IgniterService instance.
     * @param array $options
     * @param bool $force
     * @return \Themosis\Core\IgniterService
     */
    public function register($igniter, $options = [], $force = false)
    {
        if ($registered = $this->getRegistered($igniter) && !$force)
        {
            return $registered;
        }

        // If the given "igniter" is a string, we will resolve it, passing in the
        // application instance automatically for the developer. This is simply
        // a more convenient way of specifying your service provider classes.
        if (is_string($igniter))
        {
            $igniter = $this->resolveIgniterClass($igniter);
        }

        $igniter->ignite();

        // Once we have registered the service we will iterate through the options
        // and set each of them on the application so they will be available on
        // the actual loading of the service objects and for developer usage.
        foreach ($options as $key => $value)
        {
            $this[$key] = $value;
        }

        $this->markAsRegistered($igniter);

        return $igniter;
    }

    /**
     * Get the registered igniter service instance if it exists.
     *
     * @param  \Themosis\Core\IgniterService|string $igniter
     * @return \Themosis\Core\IgniterService|null
     */
    public function getRegistered($igniter)
    {
        $name = is_string($igniter) ? $igniter : get_class($igniter);

        if (array_key_exists($name, $this->loadedIgniters))
        {
            return array_first($this->serviceIgniters, function($key, $value) use ($name)
            {
                return get_class($value) == $name;
            });
        }
    }

    /**
     * Create an IgniterService instance and pass it the Application instance.
     *
     * @param string $igniter The IgniterService class name.
     * @return \Themosis\Core\IgniterService
     */
    public function resolveIgniterClass($igniter)
    {
        return new $igniter($this);
    }

    /**
     * Run the front-end application and send the response.
     *
     * @return void
     */
    public function run()
    {
        $request = $this['request'];
        $response = $this->handle($request);
        $response->sendContent();
    }

    /**
     * Handle the given request and get the response.
     *
     * @param \Themosis\Core\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function handle(Request $request)
    {
        try
        {
            $this->refreshRequest($request = Request::createFromBase($request));
            return $this->dispatch($request);
        } catch(\Exception $e)
        {
            throw $e;
        }
    }

    /**
     * Handle the given request and get the response.
     *
     * @param  \Themosis\Core\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dispatch(Request $request)
    {
        return $this['router']->dispatch($request);
    }

    /**
     * Bind the installation paths to the application.
     *
     * @param array $paths
     * @return void
     */
    public function bindInstallPaths(array $paths)
    {
        if (isset($paths['app']))
        {
            $this->instance('path', realpath($paths['app']).DS);
        }

        // Here we will bind the install paths into the container as strings that can be
        // accessed from any point in the system. Each path key is prefixed with path
        // so that they have the consistent naming convention inside the container.
        foreach (array_except($paths, ['app']) as $key => $value)
        {
            $this->instance("path.{$key}", realpath($value).DS);
        }
    }

    /**
     * Refresh the bound request instance in the container.
     *
     * @param  \Themosis\Core\Request $request
     * @return void
     */
    protected function refreshRequest(Request $request)
    {
        $this->instance('request', $request);
        Facade::clearResolvedInstance('request');
    }

    /**
     * Mark the given igniter as registered.
     *
     * @param \Themosis\Core\IgniterService $igniter
     * @return void
     */
    protected function markAsRegistered($igniter)
    {
        $class = get_class($igniter);
        $this->serviceIgniters[] = $igniter;
        $this->loadedIgniters[$class] = true;
    }

    /**
     * Register the core class aliases in the container.
     *
     * @return void
     */
	public function registerCoreContainerAliases()
	{
		$aliases = [
            'action'            => 'Themosis\Action\ActionBuilder',
            'ajax'              => 'Themosis\Ajax\AjaxBuilder',
			'app'               => 'Themosis\Core\Application',
			'asset'             => 'Themosis\Asset\AssetFactory',
			'asset.finder'      => 'Themosis\Asset\AssetFinder',
            'config'            => 'Themosis\Configuration\ConfigFactory',
            'config.finder'     => 'Themosis\Configuration\ConfigFinder',
			'field'             => 'Themosis\Field\FieldFactory',
			'scout.compiler'    => 'Themosis\View\Compilers\ScoutCompiler',
			'form'              => 'Themosis\Html\FormBuilder',
			'html'              => 'Themosis\Html\HtmlBuilder',
			'loop'              => 'Themosis\View\Loop',
			'metabox'           => 'Themosis\Metabox\MetaboxBuilder',
			'page'              => 'Themosis\Page\PageBuilder',
			'posttype'          => 'Themosis\PostType\PostTypeBuilder',
			'request'           => 'Themosis\Core\Request',
			'router'            => 'Themosis\Route\Router',
			'sections'          => 'Themosis\Page\Sections\SectionBuilder',
			'taxonomy'          => 'Themosis\Taxonomy\TaxonomyBuilder',
			'user'              => 'Themosis\User\UserFactory',
			'validation'        => 'Themosis\Validation\ValidationBuilder',
			'view'              => 'Themosis\View\ViewFactory'
        ];

		foreach ($aliases as $key => $alias)
		{
			$this->alias($key, $alias);
		}
	}

} 