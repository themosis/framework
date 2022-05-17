<?php

namespace Themosis\Hook;

class Action extends Hook
{
    /**
     * Run all actions registered with the hook.
     */
    public function run(string $hook, mixed ...$args): static
    {
        $this->doAction($hook, $args);

        return $this;
    }

    /**
     * Call a single action hook.
     */
    protected function doAction(string $hook, ...$args): void
    {
        do_action($hook, $args);
    }

    /**
     * Add an action event for the specified hook.
     */
    protected function addEventListener(string $name, callable $callback, int $priority, int $accepted_args): void
    {
        $this->hooks[$name] = [$callback, $priority, $accepted_args];
        $this->addAction($name, $callback, $priority, $accepted_args);
    }

    /**
     * Calls the WordPress add_action function to listen on a hook event.
     */
    protected function addAction(string $name, callable $callback, int $priority, int $accepted_args): void
    {
        add_action($name, $callback, $priority, $accepted_args);
    }
}
