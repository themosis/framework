<?php

namespace Themosis\Core\Bootstrap;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidFileException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Env;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class EnvironmentLoader
{
    /**
     * Bootstrap the application environment.
     *
     * @param Application $app
     */
    public function bootstrap(Application $app)
    {
        // Unlike Laravel, the environment is loaded even when the configuration is
        // cached: config/wordpress.php is still executed on each request to define
        // the WordPress constants, and WordPress code (themes, plugins) commonly
        // reads env() at runtime.
        $this->checkForSpecificEnvironmentFile($app);

        try {
            $this->createDotenv($app)->safeLoad();
        } catch (InvalidFileException $e) {
            $this->writeErrorAndDie($e);
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
                $app->environmentFile() . '.' . $input->getParameterOption('--env'),
            )) {
                return;
            }
        }

        if (! env('APP_ENV')) {
            return;
        }

        $this->setEnvironmentFilePath(
            $app,
            $app->environmentFile() . '.' . env('APP_ENV'),
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
        if (file_exists($app->environmentPath() . '/' . $file)) {
            $app->loadEnvironmentFrom($file);

            return true;
        }

        return false;
    }

    /**
     * Create a Dotenv instance.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return Dotenv
     */
    protected function createDotenv($app)
    {
        return Dotenv::create(
            Env::getRepository(),
            $app->environmentPath(),
            $app->environmentFile(),
        );
    }

    /**
     * Write the error information to the screen and exit.
     *
     * @param InvalidFileException $e
     */
    protected function writeErrorAndDie(InvalidFileException $e)
    {
        $output = (new ConsoleOutput())->getErrorOutput();

        $output->writeln('The environment file is invalid!');
        $output->writeln($e->getMessage());

        die(1);
    }
}
