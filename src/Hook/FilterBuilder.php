<?php

namespace Themosis\Hook;

class FilterBuilder extends Hook
{
    /**
     * Run all filters registered with the hook.
     *
     * @param string $hook The filter hook name.
     * @param mixed  $args
     *
     * @return mixed
     */
    public function run($hook, $args = null)
    {
        if (is_array($args)) {
            return $this->applyFiltersRefArray($hook, $args);
        }

        return $this->applyFilters($hook, $args);
    }

    /**
     * Call a filter hook with data as an array.
     *
     * @param string $hook The hook name.
     * @param array  $args Filter data passed with the hook as an array.
     *
     * @return mixed
     */
    protected function applyFiltersRefArray($hook, array $args)
    {
        return apply_filters_ref_array($hook, $args);
    }

    /**
     * Call a filter hook.
     *
     * @param string $hook The hook name.
     * @param mixed  $args Filter data passed with the hook.
     *
     * @return mixed
     */
    protected function applyFilters($hook, $args)
    {
        return apply_filters($hook, $args);
    }

    /**
     * Add a filter event for the specified hook.
     *
     * @param string                $name
     * @param \Closure|string|array $callback
     * @param int                   $priority
     * @param int                   $accepted_args
     */
    protected function addEventListener($name, $callback, $priority, $accepted_args)
    {
        $this->hooks[$name] = [$callback, $priority, $accepted_args];
        $this->addFilter($name, $callback, $priority, $accepted_args);
    }

    /**
     * Calls the WordPress add_filter function in order to listen to a filter hook.
     *
     * @param string                $name
     * @param \Closure|string|array $callback
     * @param int                   $priority
     * @param int                   $accepted_args
     */
    protected function addFilter($name, $callback, $priority, $accepted_args)
    {
        add_filter($name, $callback, $priority, $accepted_args);
    }
}
