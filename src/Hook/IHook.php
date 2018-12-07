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
    public function add($hooks, $callback, $priority = 10, $accepted_args = 2);

    /**
     * Run all events registered with the hook.
     *
     * @param string $hook The event hook name.
     * @param mixed  $args
     *
     * @return mixed
     */
    public function run($hook, $args = null);

    /**
     * Check if a registered hook exists.
     *
     * @param string $hook
     *
     * @return bool
     */
    public function exists($hook);

    /**
     * Return the callback registered with the given hook.
     *
     * @param string $hook The hook name.
     *
     * @return array|bool
     */
    public function getCallback($hook);

    /**
     * Remove a defined action or filter.
     *
     * @param string          $hook     The hook name.
     * @param \Closure|string $callback The callback to remove.
     * @param int             $priority The priority number.
     *
     * @return mixed
     */
    public function remove($hook, $callback = null, $priority = 10);
}
