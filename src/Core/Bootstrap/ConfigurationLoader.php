<?php

namespace Themosis\Core\Bootstrap;

use Exception;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Config\Repository as RepositoryContract;
use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\Finder\Finder;

class ConfigurationLoader
{
    public function bootstrap(Application $app)
    {
        $items = [];

        /*
         * Verify if configuration is cached. If so, fetch it
         * to avoid parsing all config files.
         */
        if (is_file($cached = $app->getCachedConfigPath())) {
            $items = require $cached;
            $loadedFromCache = true;
        }

        /*
         * Load configuration repository.
         */
        $app->instance('config', $config = new Repository($items));

        if (! isset($loadedFromCache)) {
            $this->loadConfigurationFiles($app, $config);
        } else {
            $this->maybeForceWpConfigInclude();
        }

        /*
         * Let's set the application environment based on received
         * configuration.
         */
        $app->detectEnvironment(function () use ($config) {
            return $config->get('app.env', 'production');
        });

        /*
         * date_default_timezone_set is set by default to UTC by WordPress.
         */
        mb_internal_encoding($config->get('app.charset'));
    }

    /**
     * Load configuration items from all found config files.
     *
     *
     * @throws Exception
     */
    protected function loadConfigurationFiles(Application $app, RepositoryContract $repository): void
    {
        $files = $this->getConfigurationFiles($app);

        if (! isset($files['app'])) {
            throw new Exception('Unable to load the "app" configuration file.');
        }
        foreach ($files as $key => $path) {
            // Avoid duplicate constant definitions.
            if ('wordpress' === $key && $this->isWordPressConfigLoaded()) {
                continue;
            }

            $repository->set($key, require $path);
        }
    }

    /**
     * Do we need to include the wordpress config file if cached config is loaded?
     */
    protected function maybeForceWpConfigInclude(): void
    {
        // Avoid duplicate constants definitions.
        if ($this->isWordPressConfigLoaded()) {
            return;
        }
        $cacheConfig = app()->getCachedConfigPath('config.php');

        if (! file_exists($cacheConfig)) {
            return;
        }

        require_once app()->configPath('wordpress.php');
    }

    /**
     * Check if the WordPress config constants are already defined.
     */
    protected function isWordPressConfigLoaded(): bool
    {
        return defined('AUTH_KEY');
    }

    /**
     * Get all configuration files.
     *
     *
     * @return array
     */
    protected function getConfigurationFiles(Application $app)
    {
        $files = [];

        foreach (Finder::create()->files()->name('*.php')->in($app->configPath()) as $file) {
            $directory = $this->getNestedDirectory($file, $app->configPath());

            $files[$directory.basename($file->getRealPath(), '.php')] = $file->getRealPath();
        }

        ksort($files, SORT_NATURAL);

        return $files;
    }

    /**
     * Get configuration file nesting path.
     *
     * @param  string  $path
     * @return string
     */
    protected function getNestedDirectory(\SplFileInfo $file, $path)
    {
        $directory = $file->getPath();

        if ($nested = trim(str_replace($path, '', $directory), DIRECTORY_SEPARATOR)) {
            $nested = str_replace(DIRECTORY_SEPARATOR, '.', $nested).'.';
        }

        return $nested;
    }
}
