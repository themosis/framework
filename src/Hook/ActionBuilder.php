<?php

namespace Themosis\Hook;

use Themosis\Hook\Support\ArgumentCountCalculator;

class ActionBuilder extends Hook
{
    /**
     * Run all actions registered with the hook.
     *
     * @param string $hook The action hook name.
     * @param mixed  $args
     *
     * @return $this
     */
    public function run(string $hook, $args = null): self
    {
        if (is_array($args)) {
            $this->doActionRefArray($hook, $args);
        } else {
            $this->doAction($hook, $args);
        }

        return $this;
    }

    /**
     * Call a single action hook.
     *
     * @param string $hook The hook name.
     * @param mixed  $args Arguments passed to the hook.
     */
    protected function doAction(string $hook, $args)
    {
        do_action($hook, $args);
    }

    /**
     * Call a single action hook with arguments as an array.
     *
     * @param string $hook The hook name.
     * @param array  $args Arguments passed as an array to the hook.
     */
    protected function doActionRefArray(string $hook, array $args)
    {
        do_action_ref_array($hook, $args);
    }

    /**
     * Add an action event for the specified hook.
     *
     * @param string                $name
     * @param \Closure|string|array $callback
     * @param int                   $priority
     * @param int                   $accepted_args
     */
    protected function addEventListener($name, $callback, int $priority, $accepted_args)
    {
        $this->hooks[$name] = [$callback, $priority, $accepted_args];
        $this->addAction($name, $callback, $priority, $accepted_args);
    }

    /**
     * Calls the WordPress add_action function to listen on a hook event.
     *
     * @param string                $name
     * @param \Closure|string|array $callback
     * @param int                   $priority
     * @param int                   $accepted_args
     */
    protected function addAction($name, $callback, int $priority, $accepted_args)
    {
        add_filter($name, $callback, $priority, $accepted_args);
    }
}
