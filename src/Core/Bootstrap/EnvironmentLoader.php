<?php

namespace Themosis\Core\Bootstrap;

use Dotenv\Dotenv;
use Dotenv\Environment\Adapter\EnvConstAdapter;
use Dotenv\Environment\Adapter\ServerConstAdapter;
use Dotenv\Environment\DotenvFactory;
use Dotenv\Exception\InvalidFileException;
use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

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
            $dotenv = $this->createDotenv($app);
            $dotenv->safeLoad();
            $dotenv->required($this->required);
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
            $app->environmentPath(),
            $app->environmentFile(),
            new DotenvFactory([new EnvConstAdapter(), new ServerConstAdapter()])
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
