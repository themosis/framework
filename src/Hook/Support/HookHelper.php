<?php

namespace Themosis\Hook\Support;

use Themosis\Hook\Contracts\Hookable;
use ReflectionClass;

final class HookHelper
{
    public static function isHookable($callback): bool
    {
        return is_string($callback) && class_exists($callback) && (new ReflectionClass($callback))->implementsInterface(Hookable::class);
    }
}