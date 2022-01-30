<?php

namespace Themosis\Core\Console;

use Illuminate\Console\GeneratorCommand;

class HookMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:hook';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new hook class';

    /**
     * The type of the class being generated.
     *
     * @var string
     */
    protected $type = 'Hook';

    /**
     * Return the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/hook.stub';
    }

    /**
     * Return the class default namespace.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Hooks';
    }
}
