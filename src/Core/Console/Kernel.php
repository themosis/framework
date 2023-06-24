<?php

namespace Themosis\Core\Console;

use Illuminate\Console\Application as Artisan;
use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Console\Kernel as KernelContract;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Env;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Throwable;

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
    protected $artisan;

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
        \Themosis\Core\Bootstrap\BootProviders::class,
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
            define('ARTISAN_BINARY', 'artisan');
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
            return tap(new Schedule($this->scheduleTimezone()), function ($schedule) {
                $this->schedule($schedule->useCache($this->scheduleCache()));
            });
        });
    }

    /**
     * Get the name of the cache store that should manage scheduling mutexes.
     *
     * @return string
     */
    protected function scheduleCache()
    {
        return Env::get('SCHEDULE_CACHE_DRIVER');
    }

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }

    /**
     * Get the timezone that should be used by default for scheduled events.
     *
     * @return \DateTimeZone|string|null
     */
    protected function scheduleTimezone()
    {
        $config = $this->app['config'];

        return $config->get('app.schedule_timezone', $config->get('app.timezone'));
    }

    /**
     * Run the console application.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  null  $output
     * @return int
     */
    public function handle($input, $output = null)
    {
        try {
            $this->bootstrap();

            return $this->getArtisan()->run($input, $output);
        } catch (\Throwable $e) {
            $this->reportException($e);
            $this->renderException($output, $e);

            return 1;
        }
    }

    /**
     * Terminate the application.
     *
     * @param  InputInterface  $input
     * @param  int  $status
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
     * @param  string  $signature
     * @return ClosureCommand
     */
    public function command($signature, \Closure $callback)
    {
        $command = new ClosureCommand($signature, $callback);

        Artisan::starting(function ($artisan) use ($command) {
            $artisan->add($command);
        });

        return $command;
    }

    /**
     * Register the given command with the console applicationb.
     *
     * @param  \Symfony\Component\Console\Command\Command  $command
     */
    public function registerCommand($command)
    {
        $this->getArtisan()->add($command);
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
     * @param  array|string  $paths
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
                Str::after($command->getPathname(), app_path().DIRECTORY_SEPARATOR),
            );

            if (is_subclass_of($command, Command::class) && ! (new \ReflectionClass($command))->isAbstract()) {
                Artisan::starting(function ($artisan) use ($command) {
                    $artisan->resolve($command);
                });
            }
        }
    }

    /**
     * Return the artisan application instance.
     *
     * @return \Illuminate\Console\Application
     */
    protected function getArtisan()
    {
        if (is_null($this->artisan)) {
            $artisan = new Artisan($this->app, $this->events, $this->app->version());
            $artisan->setName('Themosis Framework');

            return $this->artisan = $artisan->resolveCommands($this->commands);
        }

        return $this->artisan;
    }

    /**
     * Alias for "getArtisan()".
     */
    protected function getConsole()
    {
        return $this->getArtisan();
    }

    /**
     * Alias. Set the console application instance.
     *
     * @param  \Illuminate\Console\Application  $console
     */
    public function setConsole($console)
    {
        $this->setArtisan($console);
    }

    /**
     * Set the console application instance.
     *
     * @param  \Illuminate\Console\Application  $artisan
     */
    public function setArtisan($artisan)
    {
        $this->artisan = $artisan;
    }

    /**
     * Run a console command by name.
     *
     * @param  string  $command
     * @param  OutputInterface  $outputBuffer
     * @return int
     */
    public function call($command, array $parameters = [], $outputBuffer = null)
    {
        $this->bootstrap();

        return $this->getArtisan()->call($command, $parameters, $outputBuffer);
    }

    /**
     * Queue the given console command.
     *
     * @param  string  $command
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

        return $this->getArtisan()->all();
    }

    /**
     * Get the output for the last run command.
     *
     * @return string
     */
    public function output()
    {
        $this->bootstrap();

        return $this->getArtisan()->output();
    }

    /**
     * Report the exception to the exception handler.
     */
    protected function reportException(Throwable $e)
    {
        $this->app[ExceptionHandler::class]->report($e);
    }

    /**
     * Render the exception.
     *
     * @param  OutputInterface  $output
     */
    protected function renderException($output, Throwable $e)
    {
        $this->app[ExceptionHandler::class]->renderForConsole($output, $e);
    }
}
