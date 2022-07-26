<?php

namespace Themosis\Core\Console;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class SchedulePostCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'schedule:post {--id= --post-type=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schedule the publication of one or more posts.';

    public function handle(): void
    {
        /**
         * If no options, schedule all posts of any post-types.
         * --id: schedule a post with a specific ID.
         * --post-type: schedule any posts with the given post-type.
         */
        $posts = $this->findPosts();
    }

    protected function findPosts(): Collection
    {
    }
}
