<?php

namespace Themosis\Core\Console;

use Illuminate\Console\Command;

class DownCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'down {--time= : The number of seconds to keep the application in maintenance mode.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Put the application into maintenance mode';

    /**
     * Execute the command.
     */
    public function handle()
    {
        file_put_contents(
            web_path(config('app.wp.dir').'/.maintenance'),
            '<?php $upgrading = '.$this->getDuration().'; ?>'
        );

        $this->comment('Application is now in maintenance mode.');
    }

    /**
     * Return the maintenance duration.
     *
     * @return int|string
     */
    protected function getDuration()
    {
        $time = $this->option('time');

        return is_numeric($time) && $time > 0 ? (int) ((time() - (10 * 60)) + $time) : 'time()';
    }
}
