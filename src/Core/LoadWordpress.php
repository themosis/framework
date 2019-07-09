<?php

namespace Themosis\Core;

trait LoadWordpress
{
    /**
     * Load Wordpress's wp-load.php file
     */
    protected function loadWordpress(): void
    {
        if ($this->loadWp) {
            require_once web_path('cms/wp-load.php');
        }
    }
}
