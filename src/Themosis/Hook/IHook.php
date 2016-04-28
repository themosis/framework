<?php

namespace Themosis\Hook;

interface IHook
{
    /**
     * Add event using the WordPress hooks.
     *
     * @param string          $hook     The hook name.
     * @param \Closure|string $callback Using a class method like so "MyClass@method"
     *
     * @return mixed
     */
    public function add($hook, $callback);

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
}
