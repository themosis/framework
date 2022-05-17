<?php

namespace Themosis\Hook;

interface IHook
{
    /**
     * Add event using the WordPress hooks.
     */
    public function add(string | array $hooks, callable $callback, int $priority = 10, int $accepted_args = 3): static;

    /**
     * Run all events registered with the hook.
     */
    public function run(string $hook, mixed ...$args): mixed;

    /**
     * Check if a registered hook exists.
     *
     * @param string $hook
     *
     * @return bool
     */
    public function exists(string $hook): bool;

    /**
     * Return the callback registered with the given hook.
     *
     * @param string $hook The hook name.
     *
     * @return ?callable
     */
    public function getCallback(string $hook): ?callable;

    /**
     * Remove a defined action or filter.
     *
     * @param string    $hook     The hook name.
     * @param ?callable $callback The callback to remove.
     * @param int       $priority The priority number.
     *
     * @return mixed
     */
    public function remove(string $hook, callable $callback = null, int $priority = 10): bool | static;
}
