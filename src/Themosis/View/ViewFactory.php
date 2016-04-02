<?php
namespace Themosis\View;

use Themosis\Action\IAction;
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
    protected $shared = [];

    /**
     * The view extensions.
     *
     * @var array
     */
    protected $extensions = ['scout.php' => 'scout', 'php' => 'php'];

    /**
     * A list of captured sections.
     *
     * @var array
     */
    protected $sections = [];

    /**
     * A stack of in-progress sections.
     *
     * @var array
     */
    protected $sectionStack = [];

    /**
     * The number of active rendering operations.
     *
     * @var int
     */
    protected $renderCount = 0;

    /**
     * The action/event handler.
     *
     * @var \Themosis\Action\IAction
     */
    protected $action;

    /**
     * Define a ViewFactory instance.
     *
     * @param Engines\EngineResolver $engines The available engines.
     * @param ViewFinder $finder
     * @param \Themosis\Action\IAction $action
     */
    public function __construct(EngineResolver $engines, ViewFinder $finder, IAction $action)
    {
        $this->engines = $engines;
        $this->finder = $finder;
        $this->action = $action;

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
    public function make($view, array $datas = [])
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
        if (!is_array($key))
        {
            return $this->shared[$key] = $value;
        }
        else
        {
            foreach ($key as $innerKey => $val)
            {
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
     * Allows you to register multiple view composers at once.
     *
     * @param array $composers A list of view composers
     * @return array
     */
    public function composers(array $composers)
    {
        $registered = [];

        foreach ($composers as $callback => $views)
        {
            $registered += $this->composer($views, $callback);
        }

        return $registered;
    }

    /**
     * Register a view composer event.
     *
     * @param string|array $views The view(s) name
     * @param \Closure|string $callback The closure or class to register
     * @return array
     */
    public function composer($views, $callback)
    {
        $composers = [];

        foreach ((array) $views as $view)
        {
            $hook = 'composing: '.$view;
            $composers[] = $this->action->add($hook, $callback);
        }

        return $composers;
    }

    /**
     * Run the composer events for a specific view.
     *
     * @param View $view
     * @return void
     */
    public function callComposer(View $view)
    {
        $hook = 'composing: '.$view->getName();

        if ($this->action->exists($hook))
        {
            do_action($hook, $view);
        }
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

        foreach ($extensions as $extension)
        {
            $end = substr($path, -strlen($extension));

            if ($end === $extension)
            {
                return $extension;
            }
        }
    }

    /**
     * Get the rendered contents of a partial from a loop.
     *
     * @param string $view
     * @param array $data
     * @param string $iterator
     * @param string $empty
     * @return string
     */
    public function renderEach($view, $data, $iterator, $empty = 'raw|')
    {
        $result = '';

        // If is actually data in the array, we will loop through the data and append
        // an instance of the partial view to the final result HTML passing in the
        // iterated value of this data array, allowing the views to access them.
        if (count($data) > 0)
        {
            foreach ($data as $key => $value)
            {
                $data = ['key' => $key, $iterator => $value];

                $result .= $this->make($view, $data)->render();
            }
        }
        else
        {
            // If there is no data in the array, we will render the contents of the empty
            // view. Alternatively, the "empty view" could be a raw string that begins
            // with "raw|" for convenience and to let this know that it is a string.
            if (starts_with($empty, 'raw|'))
            {
                $result = substr($empty, 4);
            }
            else
            {
                $result = $this->make($empty)->render();
            }
        }

        return $result;
    }

    /**
     * Get the string content of a section.
     *
     * @param string $section
     * @param string $default
     * @return string
     */
    public function yieldContent($section, $default = '')
    {
        return isset($this->sections[$section]) ? $this->sections[$section] : $default;
    }

    /**
     * Start injecting content into a section.
     *
     * @param string $section
     * @param string $content
     * @return void
     */
    public function startSection($section, $content = '')
    {
        if ($content === '')
        {
            ob_start() && array_push($this->sectionStack, $section);
        }
        else
        {
            $this->extendSection($section, $content);
        }
    }

    /**
     * Append content to a given section.
     *
     * @param string $section
     * @param string $content
     * @return void
     */
    protected function extendSection($section, $content)
    {
        if (isset($this->sections[$section]))
        {
            $content = str_replace('@parent', $content, $this->sections[$section]);
            $this->sections[$section] = $content;
        }
        else
        {
            $this->sections[$section] = $content;
        }
    }

    /**
     * Stop injecting content into a section.
     *
     * @param bool $overwrite
     * @return string
     */
    public function stopSection($overwrite = false)
    {
        $last = array_pop($this->sectionStack);

        if ($overwrite)
        {
            $this->sections[$last] = ob_get_clean();
        }
        else
        {
            $this->extendSection($last, ob_get_clean());
        }

        return $last;
    }

    /**
     * Stop injecting content into a section and return its contents.
     *
     * @return string
     */
    public function yieldSection()
    {
        return $this->yieldContent($this->stopSection());
    }

    /**
     * Flush all of the section contents if done rendering.
     *
     * @return void
     */
    public function flushSectionsIfDoneRendering()
    {
        if ($this->doneRendering())
        {
            $this->flushSections();
        }
    }

    /**
     * Check if there are no active render operations.
     *
     * @return bool
     */
    public function doneRendering()
    {
        return $this->renderCount == 0;
    }

    /**
     * Flush all of the section contents.
     *
     * @return void
     */
    public function flushSections()
    {
        $this->sections = [];
        $this->sectionStack = [];
    }

    /**
     * Increment the rendering counter.
     *
     * @return void
     */
    public function incrementRender()
    {
        $this->renderCount++;
    }

    /**
     * Decrement the rendering counter.
     *
     * @return void
     */
    public function decrementRender()
    {
        $this->renderCount--;
    }

} 