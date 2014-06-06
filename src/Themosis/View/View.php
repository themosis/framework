<?php
namespace Themosis\View;

use ArrayAccess;
use Themosis\View\Engines\IEngine;

class View implements ArrayAccess, IRenderable {

    /**
     * View environment.
     *
     * @var ViewFactory
     */
    protected $factory;

    /**
     * The view engine.
     *
     * @var Engines\IEngine
     */
    protected $engine;

    /**
     * The view name.
     *
     * @var string
     */
    protected $view;

    /**
     * The view full path.
     *
     * @var string
     */
    protected $path;

    /**
     * View data(s).
     *
     * @var array
     */
    protected $data;

    /**
     * Define a View instance.
     *
     * @param ViewFactory $factory The view environment.
     * @param Engines\IEngine $engine The view engine.
     * @param string $view The view name.
     * @param string $path The view real path.
     * @param array $data The passed data to the view.
     */
    public function __construct(ViewFactory $factory, IEngine $engine, $view, $path, $data = array())
    {
        $this->factory = $factory;
        $this->engine = $engine;
        $this->view = $view;
        $this->path = $path;
        $this->data = $data;
    }

    /**
     * Get the evaluated content of the object.
     *
     * @return string
     */
    public function render()
    {
        $content = $this->renderContent();

        return $content;
    }

    /**
     * Get the view content.
     *
     * @return string
     */
    private function renderContent()
    {
        // @TODO Increment amount of views rendering

        $content = $this->getContent();

        // @TODO Decrement amount of views rendering

        return $content;
    }

    /**
     * Get the compiled content of the view.
     *
     * @return string
     */
    private function getContent()
    {
        return $this->engine->get($this->path, $this->gatherData());
    }

    /**
     * Merge factory and view datas. So all views can
     * get shared datas.
     *
     * @return array
     */
    private function gatherData()
    {
        $data = array_merge($this->factory->getShared(), $this->data);

        // Check if one of the 'data' is a view instance.
        // If so, evaluate its content and save it as data.
        foreach($data as $key => $value){
            if($value instanceof IRenderable){
                $data[$key] = $value->render();
            }
        }

        return $data;
    }

    /**
     * Add view data. A user can pass an array of data
     * or a single piece of data by providing its key and
     * its value.
     *
     * @param string|array $key
     * @param mixed $value
     * @return \Themosis\View\View
     */
    public function with($key, $value = null)
    {
        if(is_array($key)){

            $this->data = array_merge($this->data, $key);

        } else {

            $this->data[$key] = $value;

        }

        return $this;
    }

    /**
     * Check if a view data exists.
     *
     * @param string $key The data key name.
     * @return bool
     */
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Return a view data value.
     *
     * @param string $key The data key name.
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->data[$key];
    }

    /**
     * Set a view data.
     *
     * @param string $key The data key name.
     * @param mixed $value The data value.
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->with($key, $value);
    }

    /**
     * Remove, unset a view data.
     *
     * @param string $key
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->data[$key]);
    }
}