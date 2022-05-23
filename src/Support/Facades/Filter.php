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
 * @see \Themosis\Hook\Filter
 */
class Filter extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Themosis\Hook\Filter::class;
    }
}
