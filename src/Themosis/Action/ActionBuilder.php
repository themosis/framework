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
    protected $events = [];

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
     * @param int $priority The priority order for this action
     * @param int $accepted_args Default number of accepted arguments
     * @return \Themosis\Action\ActionBuilder
     */
    public function add($hook, $callback, $priority = 10, $accepted_args = 3)
    {
        $this->addActionEvent($hook, $callback, $priority, $accepted_args);

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
     * @param int $priority The priority order
     * @param int $accepted_args The default number of accepted arguments
     * @return \Closure|array
     */
    protected function addActionEvent($hook, $callback, $priority, $accepted_args)
    {
        // Check if $callback is a closure.
        if ($callback instanceof \Closure || is_array($callback))
        {
            $this->addEventListener($hook, $callback, $priority, $accepted_args);
            return $callback;
        }
        elseif (is_string($callback))
        {
            // Return the class responsible to handle the action.
            return $this->addClassEvent($hook, $callback, $priority, $accepted_args);
        }
    }

    /**
     * Prepare the action callback for use in a class method.
     *
     * @param string $hook
     * @param string $class
     * @param int $priority
     * @param int $accepted_args
     * @return array
     */
    protected function addClassEvent($hook, $class, $priority, $accepted_args)
    {
        $callback = $this->buildClassEventCallback($class, $hook);

        $this->addEventListener($hook, $callback, $priority, $accepted_args);

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

        return [$instance, $method];
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

        return [$class, $method];
    }

    /**
     * Add an action event for the specified hook.
     *
     * @param string $name
     * @param \Closure|string $callback
     * @param int $priority
     * @param int $accepted_args
     * @return void
     */
    protected function addEventListener($name, $callback, $priority, $accepted_args)
    {
        $this->events[$name] = add_action($name, $callback, $priority, $accepted_args);
    }
}