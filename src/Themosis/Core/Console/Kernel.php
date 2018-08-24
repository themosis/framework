<?php

namespace Themosis\Core\Console;

use Illuminate\Console\Application as Console;
use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Console\Kernel as KernelContract;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Symfony\Component\Finder\Finder;

class Kernel implements KernelContract
{
    /**
     * @var Application|\Themosis\Core\Application
     */
    protected $app;

    /**
     * @var Dispatcher
     */
    protected $events;

    /**
     * The console application instance.
     *
     * @var \Illuminate\Console\Application
     */
    protected $console;

    /**
     * @var array
     */
    protected $bootstrappers = [
        \Themosis\Core\Bootstrap\EnvironmentLoader::class,
        \Themosis\Core\Bootstrap\ConfigurationLoader::class,
        \Themosis\Core\Bootstrap\ExceptionHandler::class,
        \Themosis\Core\Bootstrap\RegisterFacades::class,
        \Themosis\Core\Bootstrap\SetRequestForConsole::class,
        \Themosis\Core\Bootstrap\RegisterProviders::class,
        \Themosis\Core\Bootstrap\BootProviders::class
    ];

    /**
     * The console commands provided by the application.
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Indicates if the Closure commands have been loaded.
     *
     * @var bool
     */
    protected $commandsLoaded = false;

    public function __construct(Application $app, Dispatcher $events)
    {
        if (! defined('ARTISAN_BINARY')) {
            define('ARTISAN_BINARY', 'console');
        }

        $this->app = $app;
        $this->events = $events;

        $this->app->booted(function () {
            $this->defineConsoleSchedule();
        });
    }

    /**
     * Define the application's command schedule.
     */
    protected function defineConsoleSchedule()
    {
        $this->app->singleton(Schedule::class, function ($app) {
            return new Schedule();
        });

        $schedule = $this->app->make(Schedule::class);

        $this->schedule($schedule);
    }

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }

    /**
     * Run the console application.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param null                                            $output
     *
     * @return int
     */
    public function handle($input, $output = null)
    {
        try {
            $this->bootstrap();

            return $this->getConsole()->run($input, $output);
        } catch (\Exception $e) {
            $this->reportException($e);
            $this->renderException($output, $e);

            return 1;
        } catch (\Throwable $e) {
            $e = new FatalThrowableError($e);

            $this->reportException($e);
            $this->renderException($output, $e);

            return 1;
        }
    }

    /**
     * Terminate the application.
     *
     * @param InputInterface $input
     * @param int            $status
     */
    public function terminate($input, $status)
    {
        $this->app->terminate();
    }

    /**
     * Bootstrap the application for console commands.
     */
    public function bootstrap()
    {
        if (! $this->app->hasBeenBootstrapped()) {
            $this->app->bootstrapWith($this->bootstrappers());
        }

        $this->app->loadDeferredProviders();

        if (! $this->commandsLoaded) {
            $this->commands();

            $this->commandsLoaded = true;
        }
    }

    /**
     * Register the Closure based commands for the application.
     */
    protected function commands()
    {
        //
    }

    /**
     * Register a closure based command with the application.
     *
     * @param string   $signature
     * @param \Closure $callback
     *
     * @return ClosureCommand
     */
    public function command($signature, \Closure $callback)
    {
        $command = new ClosureCommand($signature, $callback);

        Console::starting(function ($console) use ($command) {
            $console->add($command);
        });

        return $command;
    }

    /**
     * Register the given command with the console applicationb.
     *
     * @param \Symfony\Component\Console\Command\Command $command
     */
    public function registerCommand($command)
    {
        $this->getConsole()->add($command);
    }

    /**
     * Get the bootstrap classes for the application.
     *
     * @return array
     */
    protected function bootstrappers()
    {
        return $this->bootstrappers;
    }

    /**
     * Register all of the commands in the given directory.
     *
     * @param array|string $paths
     *
     * @throws \ReflectionException
     */
    protected function load($paths)
    {
        $paths = array_unique(is_array($paths) ? $paths : (array) $paths);

        $paths = array_filter($paths, function ($path) {
            return is_dir($path);
        });

        if (empty($paths)) {
            return;
        }

        $namespace = $this->app->getNamespace();

        foreach ((new Finder())->in($paths)->files() as $command) {
            $command = $namespace.str_replace(
                ['/', '.php'],
                ['\\', ''],
                Str::after($command->getPathname(), app_path().DIRECTORY_SEPARATOR)
            );

            if (is_subclass_of($command, Command::class) && ! (new \ReflectionClass($command))->isAbstract()) {
                Console::starting(function ($console) use ($command) {
                    $console->resolve($command);
                });
            }
        }
    }

    /**
     * Method alias for compatibility.
     */
    protected function getArtisan()
    {
        return $this->getConsole();
    }

    /**
     * Return the console application instance.
     *
     * @return \Illuminate\Console\Application
     */
    protected function getConsole()
    {
        if (is_null($this->console)) {
            $console = new Console($this->app, $this->events, $this->app->version());
            $console->setName('Themosis Framework');

            return $this->console = $console->resolveCommands($this->commands);
        }

        return $this->console;
    }

    /**
     * Set the console application instance.
     *
     * @param \Illuminate\Console\Application $console
     */
    public function setConsole($console)
    {
        $this->console = $console;
    }

    /**
     * Alias. Set the console application instance.
     *
     * @param \Illuminate\Console\Application $artisan
     */
    public function setArtisan($artisan)
    {
        $this->setConsole($artisan);
    }

    /**
     * Run a console command by name.
     *
     * @param string          $command
     * @param array           $parameters
     * @param OutputInterface $outputBuffer
     *
     * @return int
     */
    public function call($command, array $parameters = [], $outputBuffer = null)
    {
        $this->bootstrap();

        return $this->getConsole()->call($command, $parameters, $outputBuffer);
    }

    /**
     * Queue the given console command.
     *
     * @param string $command
     * @param array  $parameters
     *
     * @return \Themosis\Core\Bus\PendingDispatch
     */
    public function queue($command, array $parameters = [])
    {
        return QueueCommand::dispatch(func_get_args());
    }

    /**
     * Get all registered commands with the console.
     *
     * @return array
     */
    public function all()
    {
        $this->bootstrap();

        return $this->getConsole()->all();
    }

    /**
     * Get the output for the last run command.
     *
     * @return string
     */
    public function output()
    {
        $this->bootstrap();

        return $this->getConsole()->output();
    }

    /**
     * Report the exception to the exception handler.
     *
     * @param \Exception $e
     */
    protected function reportException(\Exception $e)
    {
        $this->app[ExceptionHandler::class]->report($e);
    }

    /**
     * Render the exception.
     *
     * @param OutputInterface $output
     * @param \Exception      $e
     */
    protected function renderException($output, \Exception $e)
    {
        $this->app[ExceptionHandler::class]->renderForConsole($output, $e);
    }
}
