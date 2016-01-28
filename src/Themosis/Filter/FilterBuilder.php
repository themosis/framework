<?php
namespace Themosis\Filter;

use Themosis\Core\Container;

class FilterBuilder implements IFilter
{
    /**
     * The IoC container.
     *
     * @var \Themosis\Core\Container
     */
    protected $container;

    /**
     * List of registered filters.
     *
     * @var array
     */
    protected $events = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Wrapper of the "add_filter" function. Allows
     * a developer to specify a controller class or closure.
     *
     * @param string $hook The filter hook name
     * @param \Closure|string $callback The closure or class to use
     * @param int $priority The priority order for this filter
     * @param int $accepted_args Default number of accepted arguments
     * @return \Themosis\Action\ActionBuilder
     */
    public function add($hook, $callback, $priority = 10, $accepted_args = 3)
    {
        $this->addFilterEvent($hook, $callback, $priority, $accepted_args);

        return $this;
    }

    /**
     * Run all filters/events registered by the hook.
     *
     * @param string $hook The filter hook  name.
     * @param mixed $args
     * @return mixed
     */
    public function run($hook, $args = null)
    {
        if (is_array($args))
        {
            apply_filters_ref_array($hook, $args);
        }
        else
        {
            apply_filters($hook, $args);
        }

        return $this;
    }

    /**
     * Check if a registered filter exists.
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
    protected function addFilterEvent($hook, $callback, $priority, $accepted_args)
    {
        // Check if $callback is a closure.
        if ($callback instanceof \Closure || is_array($callback))
        {
            $this->addEventListener($hook, $callback, $priority, $accepted_args);
            return $callback;
        }
        elseif (is_string($callback))
        {
            // Return the class responsible to handle the filter.
            return $this->addClassEvent($hook, $callback, $priority, $accepted_args);
        }
    }

    /**
     * Prepare the filter callback for use in a class method.
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
     * Add a filter event for the specified hook.
     *
     * @param string $name
     * @param \Closure|string $callback
     * @param int $priority
     * @param int $accepted_args
     * @return void
     */
    protected function addEventListener($name, $callback, $priority, $accepted_args)
    {
        $this->events[$name] = add_filter($name, $callback, $priority, $accepted_args);
    }
}
