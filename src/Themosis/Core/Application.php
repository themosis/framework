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
    protected $serviceIgniters = array();

    /**
     * The names of the loaded service igniters.
     *
     * @var array
     */
    protected $loadedIgniters = array();

    /**
     * The deferred services and their igniters.
     *
     * @var array
     */
    protected $deferredServices = array();

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
        return forward_static_call(array(static::$requestClass, 'createFromGlobals'));
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
    protected function registerCoreIgniters()
    {
        $services = array(

            'asset'         => '\Themosis\Asset\AssetIgniterService',
            'field'         => '\Themosis\Field\FieldIgniterService',
            'form'          => '\Themosis\Html\FormIgniterService',
            'html'          => '\Themosis\Html\HtmlIgniterService',
            'metabox'       => '\Themosis\Metabox\MetaboxIgniterService',
            'page'          => '\Themosis\Page\PageIgniterService',
            'posttype'      => '\Themosis\PostType\PostTypeIgniterService',
            'router'        => '\Themosis\Route\RouteIgniterService',
            'sections'      => '\Themosis\Page\Sections\SectionIgniterService',
            'taxonomy'      => '\Themosis\Taxonomy\TaxonomyIgniterService',
            'user'          => '\Themosis\User\UserIgniterService',
            'validation'    => '\Themosis\Validation\ValidationIgniterService',
            'view'          => '\Themosis\View\ViewIgniterService'

        );

        foreach ($services as $key => $value)
        {
            /**
             * Register the instance name.
             * The facade call the appropriate igniterService.
             */
            $this->igniters[$key] = $this->register($value);
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
    public function register($igniter, $options = array(), $force = false)
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

        $response = with($this)->handle($request);

        $response->send();
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

        } catch(\Exception $e){

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

} 