<?php

namespace Themosis\Database;

use Illuminate\Support\Fluent;
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

            /*
             * Retrieve already defined database connections array.
             *
             * $connections['default'] contains the MySQL configuration.
             */
            $defaultContainer = $capsule->getContainer();
            $connections = $defaultContainer['config']['database.connections'];

            /*
             * Bring the illuminate fluent (as config) for capsule compatibility.
             */
            if (!$container->bound('config')) {
                $container->instance('config', new Fluent());

                /*
                 * Bring back the $connections to the framework container.
                 */
                $container['config']['database.connections'] = $connections;
            }

            /*
             * Define the new capsule container.
             */
            $capsule->setContainer($container);

            return $capsule;
        });
    }
}
