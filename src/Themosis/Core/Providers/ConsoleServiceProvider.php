<?php

namespace Themosis\Core\Providers;

use Illuminate\Support\ServiceProvider;

class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * Defer the loading of the provider.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Commands to register.
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Development commands to register.
     *
     * @var array
     */
    protected $devCommands = [];

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerCommands(array_merge(
            $this->commands,
            $this->devCommands
        ));
    }

    /**
     * Register the given commands.
     *
     * @param array $commands
     */
    protected function registerCommands(array $commands)
    {
        foreach (array_keys($commands) as $command) {
            call_user_func_array([$this, "register{$command}Command"], []);
        }

        $this->commands(array_values($commands));
    }
}
