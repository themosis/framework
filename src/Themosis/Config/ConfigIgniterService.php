<?php
namespace Themosis\Configuration;

use Themosis\Core\IgniterService;

class ConfigIgniterService extends IgniterService
{
    /**
     * Ignite the service.
     *
     * @return void
     */
    public function ignite()
    {
        $this->registerConfigFinder();
        $this->registerConfigBuilder();
    }

    /**
     * Register the class that loads the config files.
     *
     * @return void
     */
    protected function registerConfigFinder()
    {
        $this->app->bindShared('config.finder', function($app)
        {
            // Paths to config directories.
            $paths = apply_filters('themosisConfigPaths', []);
            return new ConfigFinder($paths);
        });
    }

    /**
     * Register the config factory class.
     *
     * @return void
     */
    protected function registerConfigBuilder()
    {
        $this->app->bind('config', function($app)
        {
            return new ConfigFactory($app['config.finder']);
        });
    }
}