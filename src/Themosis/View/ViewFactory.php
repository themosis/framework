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
     * Search view files.
     *
     * @var ViewFinder
     */
    protected $finder;

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
     * The view extensions.
     *
     * @var array
     */
    protected $extensions = array('scout.php' => 'scout', 'php' => 'php');

    /**
     * Define a ViewFactory instance.
     *
     * @param Engines\EngineResolver $engines The available engines.
     * @param ViewFinder $finder
     */
    public function __construct(EngineResolver $engines, ViewFinder $finder)
    {
        $this->engines = $engines;
        $this->finder = $finder;

        // Share the factory to all views.
        $this->share('__env', $this);
    }

    /**
     * Build a view instance. This is the 1st method called
     * when defining a View.
     *
     * @param string $view The view name.
     * @param array $datas Passed data to the view.
     * @return \Themosis\View\View
     */
    public function make($view, array $datas = array())
    {
        $path = $this->finder->find($view);

        $view = new View($this, $this->getEngineFromPath($path), $view, $path, $datas);

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

    /**
     * Return view shared data.
     *
     * @return array
     */
    public function getShared()
    {
        return $this->shared;
    }

    /**
     * Fetch the engine instance regarding the view path.
     *
     * @param string $path The view full path.
     * @return \Themosis\View\Engines\IEngine
     */
    private function getEngineFromPath($path)
    {
        $engine = $this->extensions[$this->getExtension($path)];

        return $this->engines->resolve($engine);
    }

    /**
     * Return the view file extension: 'scout.php' | 'php'
     *
     * @param string $path
     * @return string
     */
    private function getExtension($path)
    {
        $extensions = array_keys($this->extensions);
        $ext = null;

        foreach($extensions as $extension){

            $end = substr($path, -strlen($extension));

            if($end === $extension){

                return $extension;

            }

        }

    }

} 