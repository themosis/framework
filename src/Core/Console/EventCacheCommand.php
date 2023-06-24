<?php

namespace Themosis\Core\Console;

use Illuminate\Console\Command;
use Themosis\Core\Support\Providers\EventServiceProvider;

class EventCacheCommand extends Command
{
    /**
     * The console command name and signature.
     *
     * @var string
     */
    protected $signature = 'event:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Discover and cache the application's events and listeners";

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->call('event:clear');

        file_put_contents(
            $this->laravel->getCachedEventsPath(),
            '<?php return '.var_export($this->getEvents(), true).';',
        );

        $this->info('Events cached successfully!');
    }

    /**
     * Return all of the events and listeners configured for the application.
     *
     * @return array
     */
    protected function getEvents()
    {
        $events = [];

        foreach ($this->laravel->getProviders(EventServiceProvider::class) as $provider) {
            $providerEvents = array_merge_recursive($provider->shouldDiscoverEvents() ? $provider->discoverEvents() : [], $provider->listens());
            $events[get_class($provider)] = $providerEvents;
        }

        return $events;
    }
}
