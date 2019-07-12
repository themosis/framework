<?php

namespace Themosis\Support\Facades;

use Illuminate\Support\Facades\Facade;
use Themosis\Html\HtmlBuilder;

/**
 * @method static HtmlBuilder setCharset()
 * @method static string attributes(array $attributes)
 * @method static string entities(string $value)
 * @method static string special(string $value)
 *
 * @see HtmlBuilder
 */
class Html extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'html';
    }
}
