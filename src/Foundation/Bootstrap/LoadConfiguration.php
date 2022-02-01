<?php

namespace Themosis\Foundation\Bootstrap;

use Exception;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Config\Repository as RepositoryContract;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Bootstrap\LoadConfiguration as Configuration;

class LoadConfiguration extends Configuration
{
    /**
     * WordPress configuration file name.
     *
     * @var string
     */
    protected string $wordpress = 'wordpress';

    /**
     * @param Application $app
     *
     * @return void
     *
     * @throws Exception
     */
    public function bootstrap(Application $app): void
    {
        $items = [];

        // First we will see if we have a cache configuration file. If we do, we'll load
        // the configuration items from that file so that it is very quick. Otherwise
        // we will need to spin through every configuration file and load them all.
        if (is_file($cached = $app->getCachedConfigPath())) {
            $items = require $cached;

            $loadedFromCache = true;
        }

        // Next we will spin through all of the configuration files in the configuration
        // directory and load each one into the repository. This will make all of the
        // options available to the developer for use in various parts of this app.
        $app->instance('config', $config = new Repository($items));

        if (!isset($loadedFromCache)) {
            $this->loadConfigurationFiles($app, $config);
        }

        $this->loadWordPressConfiguration($app);

        // Finally, we will set the application's environment based on the configuration
        // values that were loaded. We will pass a callback which will be used to get
        // the environment in a web context where an "--env" switch is not present.
        $app->detectEnvironment(function () use ($config) {
            return $config->get('app.env', 'production');
        });

        // WordPress is also setting this to "UTC" by default.
        date_default_timezone_set($config->get('app.timezone', 'UTC'));

        mb_internal_encoding('UTF-8');
    }

    /**
     * Load the configuration items.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param \Illuminate\Contracts\Config\Repository $repository
     * @return void
     *
     * @throws \Exception
     */
    protected function loadConfigurationFiles(Application $app, RepositoryContract $repository): void
    {
        $files = $this->getConfigurationFiles($app);

        if (!isset($files['app'])) {
            throw new Exception('Unable to load the "app" configuration file.');
        }

        foreach ($files as $key => $path) {
            if ($key === $this->wordpress) {
                continue;
            }

            $repository->set($key, require $path);
        }
    }

    /**
     * @throws Exception
     */
    protected function loadWordPressConfiguration(Application $app): void
    {
        $path = $app->configPath("{$this->wordpress}.php");

        if (! file_exists($path)) {
            throw new Exception('Unable to load the "wordpress" configuration file.');
        }

        if ($this->wordpressIsLoaded()) {
            return;
        }

        require $path;
    }

    protected function wordpressIsLoaded(): bool
    {
        return defined('AUTH_KEY');
    }
}