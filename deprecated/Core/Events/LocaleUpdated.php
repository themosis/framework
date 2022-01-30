<?php

namespace Themosis\Core\Events;

class LocaleUpdated
{
    /**
     * The updated application locale.
     *
     * @var string
     */
    public $locale;

    public function __construct($locale)
    {
        $this->locale = $locale;
    }
}
