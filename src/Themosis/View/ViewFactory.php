<?php
namespace Themosis\View;

use Themosis\Core\Container;
use Themosis\View\Engines\EngineResolver;

class ViewFactory {

    /**
     * The engines resolver instance.
     *
     * @var Engines\EngineResolver
     */
    protected $engines;

    /**
     * The container instance.
     *
     * @var \Themosis\Core\Container;
     */
    protected $container;

    /**
     * View environment shared data.
     *
     * @var array
     */
    protected $shared = array();

    /**
     * Define a ViewFactory instance.
     *
     * @param Engines\EngineResolver $engines The available engines.
     */
    public function __construct(EngineResolver $engines)
    {
        $this->engines = $engines;

        // Share the factory to all views.
        $this->share('__env', $this);
    }

    /**
     * Build a view instance. This is the 1st method called
     * when defining a View.
     *
     * @param string $view View name.
     * @param array $datas Passed data to the view.
     * @return \Themosis\View\View
     */
    public function make($view, array $datas = array())
    {
        //@TODO Find the real path of the view file.
        $path = '';

        $view = new View($this, $this->engines->resolve('scout'), $view, $path, $datas);

        return $view;
    }

    /**
     * Set the container instance.
     *
     * @param \Themosis\Core\Container $container
     * @return void
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Set the shared datas of all views.
     *
     * @param string $key The shared data name.
     * @param null $value The shared data value.
     * @return void
     */
    public function share($key, $value = null)
    {
        if(!is_array($key)){

            $this->shared[$key] = $value;

        } else {

            foreach($key as $innerKey => $val){
                $this->share($innerKey, $val);
            }

        }
    }

} 