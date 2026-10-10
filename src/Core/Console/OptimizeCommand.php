<?php

namespace Themosis\Core\Console;

use Illuminate\Console\Command;

class OptimizeCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'optimize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache the framework bootstrap files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->call('config:cache');
        $this->call('event:cache');
        $this->call('route:cache');
        $this->call('view:cache');

        $this->info('Files cached successfully!');
    }
}
