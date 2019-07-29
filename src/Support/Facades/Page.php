<?php

namespace Themosis\Support\Facades;

use Illuminate\Support\Facades\Facade;
use Themosis\Page\Contracts\PageInterface;
use Themosis\Page\PageFactory;

/**
 * @method static PageInterface make(string $slug, string $title)
 *
 * @see PageFactory
 */
class Page extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'page';
    }
}
