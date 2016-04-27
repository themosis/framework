<?php

namespace Themosis\Hook;

class FilterBuilder extends Hook
{
    /**
     * Register to an action event using the WordPress
     * hooks.
     *
     * @param string $hook The action hook name.
     * @param \Closure|string $callback Using a class method like so "MyClass@method"
     * @return mixed
     */
    public function add($hook, $callback)
    {
        // TODO: Implement add() method.
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
        // TODO: Implement run() method.
    }

    /**
     * Check if a registered action exists.
     *
     * @param string $hook
     * @return boolean
     */
    public function exists($hook)
    {
        // TODO: Implement exists() method.
    }
}
