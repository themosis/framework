<?php

namespace Themosis\Support\Facades;

use Illuminate\Support\Facades\Facade;
use Themosis\Hook\Hook;

/**
 * @method static mixed run($hook, $args = null)
 * @method static Hook add(string|array $hooks, \Closure|string|array $callback, int $priority = 10, int $accepted_args = 3)
 * @method static bool exists(string $hook)
 * @method static Hook|false remove(string $hook, \Closure|string $callback, int $priority = 10)
 * @method static array|null getCallback(string $hook)
 *
 * @see \Themosis\Hook\FilterBuilder
 */
class Filter extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'filter';
    }
}
