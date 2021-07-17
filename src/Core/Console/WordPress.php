<?php

namespace Themosis\Core\Console;

use Illuminate\Console\Command;

class WordPress extends Command
{
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        require_once web_path('cms/wp-load.php');
    }
}
