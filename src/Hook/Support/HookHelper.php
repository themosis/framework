<?php

namespace Themosis\Hook\Support;

use Themosis\Hook\Contracts\Hookable;
use ReflectionClass;

final class HookHelper
{
    /**
     * Check if class is hookable or not
     *
     * @param \Closure|string $callback
     *
     * @return bool
     */
    public static function isHookable($callback): bool
    {
        return is_string($callback) && class_exists($callback) && (new ReflectionClass($callback))->implementsInterface(Hookable::class);
    }
}