<?php
namespace Themosis\Action;

class ActionBuilder
{
    /**
     * List of called actions.
     *
     * @var array
     */
    protected $events = array();

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

        //@todo need a container instance to make it work to its best...
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
        //@todo Implement the "accepted_args" parameter.
        $this->events[$name] = add_action($name, $callback, $priority);
    }
}