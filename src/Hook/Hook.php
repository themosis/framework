<?php

namespace Themosis\Hook;

use BadMethodCallException;
use Illuminate\Contracts\Foundation\Application;
use Themosis\Hook\Support\ArgumentCountCalculator;

abstract class Hook implements IHook
{
    /**
     * @var Application
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
     * @param string|array          $hooks         The action hook name.
     * @param \Closure|string|array $callback      The action hook callback instance.
     * @param int                   $priority      The priority order for this action.
     * @param int                   $accepted_args Default number of accepted arguments.
     *
     * @throws BadMethodCallException
     *
     * @return $this
     */
    public function add($hooks, $callback, int $priority = 10, $accepted_args = null): self
    {
        foreach ((array) $hooks as $hook) {
            $accepted_args = null === $accepted_args ? (new ArgumentCountCalculator($callback))->calculate() : $accepted_args;
            $this->addHookEvent($hook, $callback, $priority, $accepted_args);
        }

        return $this;
    }

    /**
     * Check if a registered hook exists.
     *
     * @param string $hook
     *
     * @return bool
     */
    public function exists(string $hook): bool
    {
        return isset($this->hooks[$hook]);
    }

    /**
     * Remove a registered action or filter.
     *
     * @param string          $hook
     * @param \Closure|string $callback
     * @param int             $priority
     *
     * @return mixed The Hook instance or false.
     */
    public function remove(string $hook, $callback = null, int $priority = 10): self
    {
        // If $callback is null, it means we have chained the methods to
        // the action/filter instance. If the instance has no callback, return false.
        if (is_null($callback)) {
            if (! $callback = $this->getCallback($hook)) {
                return false;
            }

            list($callback, $priority, $accepted_args) = $callback;

            // Unset the hook.
            unset($this->hooks[$hook]);
        }

        $this->removeAction($hook, $callback, $priority);

        return $this;
    }

    /**
     * Return the callback registered with the hook.
     *
     * @param string $hook The hook name.
     *
     * @return array|null
     */
    public function getCallback(string $hook)
    {
        if (isset($this->hooks[$hook])) {
            return $this->hooks[$hook];
        }

        return null;
    }

    /**
     * Remove hook (filter and or action).
     *
     * @param string                $hook
     * @param \Closure|string|array $callback
     * @param int                   $priority
     */
    protected function removeAction(string $hook, $callback, int $priority)
    {
        remove_action($hook, $callback, $priority);
    }

    /**
     * Add an event for the specified hook.
     *
     * @param string                $hook          The hook name.
     * @param \Closure|string|array $callback      The hook callback instance.
     * @param int                   $priority      The priority order.
     * @param int                   $accepted_args The default number of accepted arguments.
     *
     * @throws BadMethodCallException
     *
     * @return \Closure|array|string
     */
    protected function addHookEvent(string $hook, $callback, int $priority, $accepted_args)
    {
        // Check if $callback is a closure.
        if ($callback instanceof \Closure || is_array($callback)) {
            $this->addEventListener($hook, $callback, $priority, $accepted_args);
        } elseif (is_string($callback)) {
            if (false !== strpos($callback, '@') || class_exists($callback)) {
                // Return the class responsible to handle the action.
                $callback = $this->addClassEvent($hook, $callback, $priority, $accepted_args);
            } else {
                // Used as a classic callback function.
                $this->addEventListener($hook, $callback, $priority, $accepted_args);
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
     * @throws BadMethodCallException
     *
     * @return array
     */
    protected function addClassEvent(string $hook, string $class, int $priority, int $accepted_args): array
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
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return array
     */
    protected function buildClassEventCallback(string $class, string $hook): array
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
    protected function parseClassEvent(string $class, string $hook): array
    {
        if (false !== strpos($class, '@')) {
            return explode('@', $class);
        }

        // If no method is defined, use the hook name as the method name.
        $method = false !== strpos($hook, '-') ? str_replace('-', '_', $hook) : $hook;

        return [$class, $method];
    }

    /**
     * Add an event for the specified hook.
     *
     * @param string                $name
     * @param \Closure|string|array $callback
     * @param int                   $priority
     * @param int                   $accepted_args
     *
     * @throws BadMethodCallException
     */
    protected function addEventListener(string $name, $callback, int $priority, int $accepted_args)
    {
        throw new BadMethodCallException('The "addEventListener" method must be overridden.');
    }
}
