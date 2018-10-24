<?php

namespace Themosis\Core\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ViewClearCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'view:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all compiled view files';

    /**
     * @var Filesystem
     */
    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bladePath = $this->laravel['config']['view.compiled'];
        $twigPath = $this->laravel['config']['view.twig'];

        if (! $bladePath || ! $twigPath) {
            throw new \RuntimeException('View cache path not found.');
        }

        foreach ($this->files->glob("{$bladePath}/*.php") as $view) {
            $this->files->delete($view);
        }

        $this->files->deleteDirectories($twigPath);

        $this->info('Compiled views cleared.');
    }
}
