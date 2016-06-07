<?php

namespace Themosis\Database;

use Themosis\Foundation\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{
    public function register()
    {
        if (!isset($GLOBALS['themosis.capsule'])) {
            return;
        }

        $this->app->singleton('capsule', function ($container) {
            $capsule = $GLOBALS['themosis.capsule'];
            $capsule->setContainer($container);

            return $capsule;
        });
    }
}
