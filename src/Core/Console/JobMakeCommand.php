<?php

namespace Themosis\Core\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class JobMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new job class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Job';

    /**
     * @return string
     */
    protected function getStub()
    {
        return $this->option('sync')
            ? __DIR__.'/stubs/job.stub'
            : __DIR__.'/stubs/job.queued.stub';
    }

    /**
     * Return the default class namespace.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Jobs';
    }

    /**
     * Return the command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['sync', null, InputOption::VALUE_NONE, 'Indicates that job should be synchronous'],
        ];
    }
}
