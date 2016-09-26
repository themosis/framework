<?php

namespace Themosis\Hook;

use Themosis\Foundation\Application;

abstract class Hook implements IHook
{
    /**
     * The service container.
     *
     * @var \Themosis\Foundation\Application
     */
    protected $container;

    /**
     * List of registered hooks.
     *
     * @var array
     */
    protected $hooks = [];

    /**
     * Hook constructor.
     *
     * @param Application $container
     */
    public function __construct(Application $container)
    {
        $this->container = $container;
    }

    /**
     * Wrapper of the "add_action" or "add_filter" functions. Allows
     * a developer to specify a controller class or closure.
     *
     * @param string                $hook          The action hook name.
     * @param \Closure|string|array $callback      The closure, function name or class to use, array containing an instance and its public method name.
     * @param int                   $priority      The priority order for this action.
     * @param int                   $accepted_args Default number of accepted arguments.
     *
     * @return \Themosis\Hook\ActionBuilder
     */
    public function add($hook, $callback, $priority = 10, $accepted_args = 3)
    {
        $this->addHookEvent($hook, $callback, $priority, $accepted_args);

        return $this;
    }

    /**
     * Check if a registered hook exists.
     *
     * @param string $hook
     *
     * @return bool
     */
    public function exists($hook)
    {
        if (array_key_exists($hook, $this->hooks)) {
            return true;
        }

        return false;
    }

    /**
     * Return the callback registered with the hook.
     *
     * @param string $hook The hook name.
     *
     * @return array|bool
     */
    public function getCallback($hook)
    {
        if (array_key_exists($hook, $this->hooks)) {
            return $this->hooks[$hook];
        }

        return false;
    }

    /**
     * Remove a registered action or filter.
     *
     * @param string $hook
     * @param int $priority
     * @param \Closure|string $callback
     *
     * @return mixed The Hook instance or false.
     */
    public function remove($hook, $priority = 10, $callback = null)
    {
        // If $callback is null, it means we have chained the methods to
        // the action/filter instance. If the instance has no callback, return false.
        if (is_null($callback)) {
            if (!$callback = $this->getCallback($hook)) {
                return false;
            }

            list($callback, $priority, $accepted_args) = $callback;

            // Unset the hook.
            unset($this->hooks[$hook]);
        }

        remove_action($hook, $callback, $priority);

        return $this;
    }

    /**
     * Add an event for the specified hook.
     *
     * @param string                $hook
     * @param \Closure|string|array $callback      The closure, function name or class to use, array containing an instance and its public method name.
     * @param int                   $priority      The priority order.
     * @param int                   $accepted_args The default number of accepted arguments.
     *
     * @return \Closure|array|string
     */
    protected function addHookEvent($hook, $callback, $priority, $accepted_args)
    {
        // Check if $callback is a closure.
        if ($callback instanceof \Closure || is_array($callback)) {
            $this->addEventListener($hook, $callback, $priority, $accepted_args);
        } elseif (is_string($callback)) {
            if (is_callable($callback)) {
                // Used as a classic callback function.
                $this->addEventListener($hook, $callback, $priority, $accepted_args);
            } else {
                // Return the class responsible to handle the action.
                $callback = $this->addClassEvent($hook, $callback, $priority, $accepted_args);
            }
        }

        return $callback;
    }

    /**
     * Prepare the hook callback for use in a class method.
     *
     * @param string $hook
     * @param string $class
     * @param int    $priority
     * @param int    $accepted_args
     *
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
     *
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
     *
     * @return array
     */
    protected function parseClassEvent($class, $hook)
    {
        if (str_contains($class, '@')) {
            return explode('@', $class);
        }

        // If no method is defined, use the hook name as the method name.
        $method = str_contains($hook, '-') ? str_replace('-', '_', $hook) : $hook;

        return [$class, $method];
    }

    /**
     * Add an event for the specified hook.
     *
     * @param string          $name
     * @param \Closure|string $callback
     * @param int             $priority
     * @param int             $accepted_args
     *
     * @throws HookException
     */
    protected function addEventListener($name, $callback, $priority, $accepted_args)
    {
        throw new HookException('The "addEventListener" method must be overridden.');
    }
}
