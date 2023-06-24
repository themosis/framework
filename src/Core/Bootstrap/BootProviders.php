<?php

namespace Themosis\Core\Bootstrap;

use Illuminate\Contracts\Foundation\Application;

class BootProviders
{
    /**
     * Bootstrap the application.
     */
    public function bootstrap(Application $app)
    {
        $app->boot();
    }
}
