<?php

namespace Themosis\Core\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class ListenerMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:listener';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new event listener class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Listener';

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string|string[]
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $event = $this->option('event');

        if (! Str::startsWith($event, [$this->laravel->getNamespace(), 'Illuminate', '\\', 'Themosis'])) {
            $event = $this->laravel->getNamespace().'Events\\'.$event;
        }

        $stub = str_replace('DummyEvent', class_basename($event), parent::buildClass($name));

        return str_replace('DummyFullEvent', trim($event, '\\'), $stub);
    }

    /**
     * Return the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('queued')) {
            return $this->option('event')
                ? __DIR__.'/stubs/listener-queued.stub'
                : __DIR__.'/stubs/listener-queued-duck.stub';
        }

        return $this->option('event')
            ? __DIR__.'/stubs/listener.stub'
            : __DIR__.'/stubs/listener-duck.stub';
    }

    /**
     * Check if the class already exists.
     *
     * @param  string  $rawName
     * @return bool
     */
    protected function alreadyExists($rawName)
    {
        return class_exists($rawName);
    }

    /**
     * Return the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Listeners';
    }

    /**
     * Return the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['event', 'e', InputOption::VALUE_OPTIONAL, 'The event class being listened for'],
            ['queued', null, InputOption::VALUE_NONE, 'Indicates the event listener shoud be queued'],
        ];
    }
}
