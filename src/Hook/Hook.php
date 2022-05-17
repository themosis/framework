<?php

namespace Themosis\Hook;

use BadMethodCallException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Str;

abstract class Hook implements IHook
{
    protected Application $container;

    /**
     * List of registered hooks.
     */
    protected array $hooks = [];

    public function __construct(Application $container)
    {
        $this->container = $container;
    }

    /**
     * Wrapper of the "add_action" or "add_filter" functions. Allows
     * a developer to specify a controller class or closure.
     */
    public function add(string | array $hooks, callable $callback, int $priority = 10, int $accepted_args = 3): static
    {
        foreach ((array) $hooks as $hook) {
            $this->addHookEvent($hook, $callback, $priority, $accepted_args);
        }

        return $this;
    }

    /**
     * Check if a registered hook exists.
     */
    public function exists(string $hook): bool
    {
        return array_key_exists($hook, $this->hooks);
    }

    /**
     * Remove a registered action or filter.
     */
    public function remove(string $hook, callable $callback = null, int $priority = 10): bool | static
    {
        /**
         * If $callback is null, it means we have chained the methods to
         * the action/filter instance. If the instance has no callback, return false.
         */
        if (is_null($callback)) {
            if (! $callback = $this->getCallback($hook)) {
                return false;
            }

            list($callback, $priority, $accepted_args) = $callback;

            /**
             * Unset the hook.
             */
            unset($this->hooks[$hook]);
        }

        $this->removeAction($hook, $callback, $priority);

        return $this;
    }

    /**
     * Return the callback registered with the hook.
     */
    public function getCallback(string $hook): ?callable
    {
        if (array_key_exists($hook, $this->hooks)) {
            return $this->hooks[$hook];
        }

        return null;
    }

    /**
     * Remove hook (filter and or action).
     */
    protected function removeAction(string $hook, callable $callback, int $priority): void
    {
        remove_action($hook, $callback, $priority);
    }

    /**
     * Add an event for the specified hook.
     */
    protected function addHookEvent(string $hook, callable $callback, int $priority, int $accepted_args): callable
    {
        /**
         * Check if $callback is a closure.
         */
        if ($callback instanceof \Closure || is_array($callback)) {
            $this->addEventListener($hook, $callback, $priority, $accepted_args);
        } elseif (is_string($callback)) {
            if (str_contains($callback, '@') || class_exists($callback)) {
                /**
                 * Return the class responsible to handle the action.
                 */
                $callback = $this->addClassEvent($hook, $callback, $priority, $accepted_args);
            } else {
                /**
                 * Used as a classic callback function.
                 */
                $this->addEventListener($hook, $callback, $priority, $accepted_args);
            }
        }

        return $callback;
    }

    /**
     * Prepare the hook callback for use in a class method.
     */
    protected function addClassEvent(string $hook, string $class, int $priority, int $accepted_args): array
    {
        $callback = $this->buildClassEventCallback($class, $hook);

        $this->addEventListener($hook, $callback, $priority, $accepted_args);

        return $callback;
    }

    /**
     * Build the array in order to call a class method.
     */
    protected function buildClassEventCallback(string $class, string $hook): array
    {
        list($class, $method) = $this->parseClassEvent($class, $hook);

        $instance = $this->container->make($class);

        return [$instance, $method];
    }

    /**
     * Parse a class name and returns its name and its method.
     */
    protected function parseClassEvent(string $class, string $hook): array
    {
        if (Str::contains($class, '@')) {
            return explode('@', $class);
        }

        // If no method is defined, use the hook name as the method name.
        $method = Str::contains($hook, '-') ? str_replace('-', '_', $hook) : $hook;

        return [$class, $method];
    }

    /**
     * Add an event for the specified hook.
     */
    protected function addEventListener(string $name, callable $callback, int $priority, int $accepted_args): void
    {
        throw new BadMethodCallException('The "addEventListener" method must be overridden.');
    }
}
