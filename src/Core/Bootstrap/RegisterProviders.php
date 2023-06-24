<?php

namespace Themosis\Core\Bootstrap;

use Illuminate\Contracts\Foundation\Application;

class RegisterProviders
{
    /**
     * Bootstrap application service providers.
     */
    public function bootstrap(Application $app)
    {
        $app->registerConfiguredProviders();
    }
}
