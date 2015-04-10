<?php
namespace Themosis\Action;

interface IAction
{
    /**
     * Register to an action event using the WordPress
     * hooks.
     *
     * @param string $hook The action hook name.
     * @param \Closure|string $callback Using a class method like so "MyClass@method"
     * @return mixed
     */
    public function add($hook, $callback);

    /**
     * Run all actions/events registered by the hook.
     *
     * @param string $hook The action hook  name.
     * @param mixed $args
     * @return mixed
     */
    public function run($hook, $args = null);

    /**
     * Check if a registered action exists.
     *
     * @param string $hook
     * @return boolean
     */
    public function exists($hook);
}