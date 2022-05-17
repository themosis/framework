<?php

namespace Themosis\Hook;

class Filter extends Hook
{
    /**
     * Run all filters registered with the hook.
     */
    public function run(string $hook, mixed ...$args): mixed
    {
        if (1 === count($args)) {
            return $this->applyFilters($hook, $args[0]);
        }

        return $this->applyFiltersRefArray($hook, $args);
    }

    /**
     * Call a filter hook with data as an array.
     */
    protected function applyFiltersRefArray(string $hook, array $args): mixed
    {
        return apply_filters_ref_array($hook, $args);
    }

    /**
     * Call a filter hook.
     */
    protected function applyFilters(string $hook, mixed $args): mixed
    {
        return apply_filters($hook, $args);
    }

    /**
     * Add a filter event for the specified hook.
     */
    protected function addEventListener(string $name, callable $callback, int $priority, int $accepted_args): void
    {
        $this->hooks[$name] = [$callback, $priority, $accepted_args];
        $this->addFilter($name, $callback, $priority, $accepted_args);
    }

    /**
     * Calls the WordPress add_filter function in order to listen to a filter hook.
     */
    protected function addFilter(string $name, callable $callback, int $priority, int $accepted_args): void
    {
        add_filter($name, $callback, $priority, $accepted_args);
    }
}
