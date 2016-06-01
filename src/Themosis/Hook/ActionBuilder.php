<?php

namespace Themosis\Hook;

class ActionBuilder extends Hook
{
    /**
     * Run all actions registered with the hook.
     *
     * @param string $hook The action hook name.
     * @param mixed  $args
     *
     * @return mixed
     */
    public function run($hook, $args = null)
    {
        if (is_array($args)) {
            do_action_ref_array($hook, $args);
        } else {
            do_action($hook, $args);
        }

        return $this;
    }

    /**
     * Add an action event for the specified hook.
     *
     * @param string          $name
     * @param \Closure|string $callback
     * @param int             $priority
     * @param int             $accepted_args
     */
    protected function addEventListener($name, $callback, $priority, $accepted_args)
    {
        $this->hooks[$name] = [$callback, $priority, $accepted_args];
        add_action($name, $callback, $priority, $accepted_args);
    }
}
