<?php

namespace Themosis\Core\Bootstrap;

use Dotenv\Dotenv;
use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\Console\Input\ArgvInput;

class EnvironmentLoader
{
    /**
     * Required environment variables.
     *
     * @var array
     */
    protected $required = [
        'DATABASE_NAME',
        'DATABASE_USER',
        'DATABASE_PASSWORD',
        'DATABASE_HOST',
        'APP_URL',
        'WP_URL'
    ];

    /**
     * Bootstrap the application environment.
     *
     * @param Application $app
     */
    public function bootstrap(Application $app)
    {
        if ($app->configurationIsCached()) {
            return;
        }

        $this->checkForSpecificEnvironmentFile($app);

        try {
            $dotenv = new Dotenv($app->environmentPath(), $app->environmentFile());
            $dotenv->load();
            $dotenv->required($this->required);
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
    }

    /**
     * Detect if a custom environment file matching the APP_ENV exists.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     */
    protected function checkForSpecificEnvironmentFile($app)
    {
        if ($app->runningInConsole() && ($input = new ArgvInput())->hasParameterOption('--env')) {
            if ($this->setEnvironmentFilePath(
                $app,
                $app->environmentFile().'.'.$input->getParameterOption('--env')
            )) {
                return;
            }
        }

        if (! env('APP_ENV')) {
            return;
        }

        $this->setEnvironmentFilePath(
            $app,
            $app->environmentFile().'.'.env('APP_ENV')
        );
    }

    /**
     * Load a custom environment file.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param string                                       $file
     *
     * @return bool
     */
    protected function setEnvironmentFilePath($app, $file)
    {
        if (file_exists($app->environmentPath().'/'.$file)) {
            $app->loadEnvironmentFrom($file);

            return true;
        }

        return false;
    }
}
