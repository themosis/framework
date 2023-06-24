<?php

namespace Themosis\Core\Console;

use Illuminate\Console\Command;

class UpCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'up';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bring the application out of maintenance mode';

    /**
     * Execute the command.
     */
    public function handle()
    {
        @unlink(web_path(config('app.wp.dir').'/.maintenance'));

        $this->info('Application is now live.');
    }
}
