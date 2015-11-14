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
    public function __construct(ViewFactory $factory, IEngine $engine, $view, $path, $data = [])
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

        // Flush all sections when the view is rendered.
        $this->factory->flushSectionsIfDoneRendering();

        return $content;
    }

    /**
     * Get the view content.
     *
     * @return string
     */
    private function renderContent()
    {
        // We will keep track of the amount of views being rendered so we can flush
        // the section after the complete rendering operation is done. This will
        // clear out the sections for any separate views that may be rendered.
        $this->factory->incrementRender();

        // Call the view composers
        $this->factory->callComposer($this);

        $content = $this->getContent();

        // Once we've finished rendering the view, we'll decrement the render count
        // so that each sections get flushed out next time a view is created and
        // no old sections are staying around in the memory of an environment.
        $this->factory->decrementRender();

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
        foreach ($data as $key => $value)
        {
            if ($value instanceof IRenderable)
            {
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
        if (is_array($key))
        {
            $this->data = array_merge($this->data, $key);
        }
        else
        {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * Add a view instance to the view data.
     *
     * @param string $key
     * @param string $view
     * @param array $data
     * @return \Themosis\View\View
     */
    public function nest($key, $view, array $data = [])
    {
        return $this->with($key, $this->factory->make($view, $data));
    }

    /**
     * Get the name of the view.
     *
     * @return string
     */
    public function getName()
    {
        return $this->view;
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

    /**
     * Get a piece of data from the view.
     *
     * @param string $key
     * @return mixed
     */
    public function &__get($key)
    {
        return $this->data[$key];
    }

    /**
     * Set a piece of data on the view.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->with($key, $value);
    }

    /**
     * Check if a piece of data is bound to the view.
     *
     * @param string $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Remove a piece of bound data from the view.
     *
     * @param string $key
     * @return bool
     */
    public function __unset($key)
    {
        unset($this->data[$key]);
    }

    /**
     * Get the string contents of the view.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}