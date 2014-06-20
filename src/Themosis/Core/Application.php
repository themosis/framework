<?php
namespace Themosis\Core;

use Themosis\Action\Action;
use Symfony\Component\HttpFoundation\Response;

class Application extends Container {

    protected static $requestClass = 'Themosis\Core\Request';

    /**
     * Build an Application instance.
     */
    public function __construct()
    {
        $this->registerBaseBindings($this->createNewRequest());
        $this->registerCoreIgniters();

        // Listen to front-end request.
        Action::listen('themosis_run', $this, 'run')->dispatch();
    }

    /**
     * Create a new Request instance.
     *
     * @return \Themosis\Core\Request
     */
    private function createNewRequest()
    {
        return forward_static_call(array(static::$requestClass, 'createFromGlobals'));
    }

    /**
     * Register base framework classes into the container.
     *
     * @param Request $request
     * @return void
     */
    private function registerBaseBindings(Request $request)
    {
        $this->instance('request', $request);
        $this->instance('Themosis\Core\Container', $this);
    }

    /**
     * Register all igniter services classes.
     *
     * @return void
     */
    private function registerCoreIgniters()
    {
        $services = array(

            'field'         => '\Themosis\Field\FieldIgniterService',
            'form'          => '\Themosis\Html\FormIgniterService',
            'html'          => '\Themosis\Html\HtmlIgniterService',
            'metabox'       => '\Themosis\Metabox\MetaboxIgniterService',
            'page'          => '\Themosis\Page\PageIgniterService',
            'posttype'      => '\Themosis\PostType\PostTypeIgniterService',
            'router'        => '\Themosis\Route\RouteIgniterService',
            'sections'      => '\Themosis\Page\Sections\SectionIgniterService',
            'taxonomy'      => '\Themosis\Taxonomy\TaxonomyIgniterService',
            'validation'    => '\Themosis\Validation\ValidationIgniterService',
            'view'          => '\Themosis\View\ViewIgniterService'

        );

        foreach($services as $key => $value){

            /**
             * Register the instance name.
             * The facade call the appropriate igniterService.
             */
            $this->igniters[$key] = $value;

        }
    }

    /**
     * Add the instance to the application.
     *
     * @param string $key The facade key.
     * @param callable $closure The function that call the needed instance.
     * @return void
     */
    public function bind($key, Callable $closure)
    {
        // Send the application instance to the closure.
        // Allows the container to call the dependencies.
        $this->instances[$key] = $closure($this);
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
        try{

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

} 