<?php

namespace Themosis\Core\Enums;

use Closure;
use Spatie\Enum\Enum;

/**
 * @method static self publish()
 * @method static self pending()
 * @method static self draft()
 * @method static self autoDraft()
 * @method static self future()
 * @method static self private()
 * @method static self inherit()
 * @method static self trash()
 * @method static self any()
 */
class PostStatus extends Enum
{
    protected static function values(): Closure
    {
        return function (string $name) {
            if ('autoDraft' === $name) {
                return 'auto-draft';
            }

            return $name;
        };
    }
}
