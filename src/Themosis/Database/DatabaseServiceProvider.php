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

            /*
             * Bring the illuminate fluent (as config) for capsule compatibility
             * if not defined in the service container.
             */
            if (!$container->bound('config')) {

                /*
                 * Retrieve the default container created by the "Capsule"
                 * in the framework bootstrap code.
                 */
                $defaultContainer = $capsule->getContainer();

                /*
                 * Pass default "config" class (Fluent) to the Themosis container.
                 * It automatically adds "default" database connection
                 * which refers to the WordPress MySQL.
                 */
                $container->instance('config', $defaultContainer['config']);

                /*
                 * Update the "Capsule" container with the Themosis container.
                 */
                $capsule->setContainer($container);
            }

            return $capsule;
        });
    }
}
