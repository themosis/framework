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
            apply_filters_ref_array($hook, $args);
        } else {
            apply_filters($hook, $args);
        }

        return $this;
    }

    /**
     * Add a filter event for the specified hook.
     *
     * @param string          $name
     * @param \Closure|string $callback
     * @param int             $priority
     * @param int             $accepted_args
     */
    protected function addEventListener($name, $callback, $priority, $accepted_args)
    {
        $this->hooks[$name] = [$callback, $priority, $accepted_args];
        add_filter($name, $callback, $priority, $accepted_args);
    }
}
