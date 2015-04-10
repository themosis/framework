<?php
namespace Themosis\Action;

use Themosis\Core\Container;

class ActionBuilder implements IAction
{
    /**
     * The IoC container.
     *
     * @var \Themosis\Core\Container
     */
    protected $container;

    /**
     * List of registered actions.
     *
     * @var array
     */
    protected $events = array();

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Wrapper of the "add_action" function. Allows
     * a developer to specify a controller class or closure.
     *
     * @param string $hook The action hook name
     * @param \Closure|string $callback The closure or class to use
     * @return \Themosis\Action\ActionBuilder
     */
    public function add($hook, $callback)
    {
        $this->addActionEvent($hook, $callback);

        return $this;
    }

    /**
     * Run all actions/events registered by the hook.
     *
     * @param string $hook The action hook  name.
     * @param mixed $args
     * @return mixed
     */
    public function run($hook, $args = null)
    {
        if (is_array($args))
        {
            do_action_ref_array($hook, $args);
        }

        do_action($hook, $args);

        return $this;
    }

    /**
     * Check if a registered action exists.
     *
     * @param string $hook
     * @return boolean
     */
    public function exists($hook)
    {
        if (array_key_exists($hook, $this->events))
        {
            return true;
        }

        return false;
    }

    /**
     * Add an event for the specified hook.
     *
     * @param string $hook
     * @param \Closure|string $callback If string use this syntax "Class@method"
     * @param int $priority
     * @return \Closure|array
     */
    protected function addActionEvent($hook, $callback, $priority = 10)
    {
        // Check if $callback is a closure.
        if ($callback instanceof \Closure)
        {
            $this->addEventListener($hook, $callback, $priority);
            return $callback;
        }
        elseif (is_string($callback))
        {
            // Return the class responsible to handle the action.
            return $this->addClassEvent($hook, $callback, $priority);
        }
    }

    /**
     * Prepare the action callback for use in a class method.
     *
     * @param string $hook
     * @param string $class
     * @param int $priority
     * @return array
     */
    protected function addClassEvent($hook, $class, $priority)
    {
        $callback = $this->buildClassEventCallback($class, $hook);

        $this->addEventListener($hook, $callback, $priority);

        return $callback;
    }

    /**
     * Build the array in order to call a class method.
     *
     * @param string $class
     * @param string $hook
     * @return array
     */
    protected function buildClassEventCallback($class, $hook)
    {
        list($class, $method) = $this->parseClassEvent($class, $hook);

        $instance = $this->container->make($class);

        return array($instance, $method);
    }

    /**
     * Parse a class name and returns its name and its method.
     *
     * @param string $class
     * @param string $hook
     * @return array
     */
    protected function parseClassEvent($class, $hook)
    {
        if (str_contains($class, '@'))
        {
            return explode('@', $class);
        }

        // If no method is defined, use the hook name as the method name.
        $method = str_contains($hook, '-') ? str_replace('-', '_', $hook) : $hook;

        return array($class, $method);
    }

    /**
     * Add an action event for the specified hook.
     *
     * @param string $name
     * @param \Closure|string $callback
     * @param int $priority
     * @return void
     */
    protected function addEventListener($name, $callback, $priority = 10)
    {
        //@todo Do we implement the "accepted_args" parameter
        $this->events[$name] = add_action($name, $callback, $priority);
    }
}