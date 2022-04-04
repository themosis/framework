<?php

namespace Themosis\Hook;

interface IHook
{
    /**
     * Add event using the WordPress hooks.
     *
     * @param string|array          $hooks         The hook name.
     * @param \Closure|string|array $callback      Using a class method like so "MyClass@method"
     * @param int                   $priority
     * @param int                   $accepted_args
     *
     * @return mixed
     */
    public function add($hooks, $callback, int $priority = 10, $accepted_args = null);

    /**
     * Run all events registered with the hook.
     *
     * @param string $hook The event hook name.
     * @param mixed  $args
     *
     * @return mixed
     */
    public function run(string $hook, $args = null);

    /**
     * Check if a registered hook exists.
     *
     * @param string $hook
     *
     * @return bool
     */
    public function exists(string $hook): bool;

    /**
     * Return the callback registered with the given hook.
     *
     * @param string $hook The hook name.
     *
     * @return array|bool
     */
    public function getCallback(string $hook);

    /**
     * Remove a defined action or filter.
     *
     * @param string          $hook     The hook name.
     * @param \Closure|string $callback The callback to remove.
     * @param int             $priority The priority number.
     *
     * @return mixed
     */
    public function remove(string $hook, $callback = null, int $priority = 10);
}
