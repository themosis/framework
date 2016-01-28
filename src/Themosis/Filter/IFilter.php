<?php
namespace Themosis\Filter;

interface IFilter
{
    /**
     * Register a filter using the WordPress hooks
     *
     * @param string $hook
     * @param \Closure|string $callback
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
