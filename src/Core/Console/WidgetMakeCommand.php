<?php

namespace Themosis\Core\Console;

use Illuminate\Console\GeneratorCommand;

class WidgetMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:widget';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new widget class';

    /**
     * The type of the class being generated.
     *
     * @var string
     */
    protected $type = 'Widget';

    /**
     * Return the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/widget.stub';
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
        return $rootNamespace.'\Widgets';
    }
}
