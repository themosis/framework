<?php

namespace Themosis\Core\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Themosis\Core\Support\Providers\EventServiceProvider;

class EventListCommand extends Command
{
    /**
     * The console command name and signature.
     *
     * @var string
     */
    protected $signature = 'event:list {--event= : Filter the events by name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "List the application's events and listeners";

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $events = $this->getEvents();

        if (empty($events)) {
            $this->error("Your application doesn't have any events matching the given criteria.");

            return;
        }

        $this->table(['Event', 'Listeners'], $events);
    }

    /**
     * Return all the events and listeners configured for the application.
     *
     * @return array
     */
    protected function getEvents()
    {
        $events = [];

        foreach ($this->laravel->getProviders(EventServiceProvider::class) as $provider) {
            $providerEvents = array_merge_recursive($provider->discoverEvents(), $provider->listens());
            $events = array_merge_recursive($events, $providerEvents);
        }

        if ($this->filteringByEvent()) {
            $events = $this->filterEvents($events);
        }

        return collect($events)->map(function ($listeners, $event) {
            return [
                'Event' => $event,
                'Listeners' => implode(PHP_EOL, $listeners),
            ];
        })
            ->sortBy('Event')
            ->values()
            ->toArray();
    }

    /**
     * Filter the given events using the provided event name filter.
     *
     *
     * @return array
     */
    protected function filterEvents(array $events)
    {
        if (! $eventName = $this->option('event')) {
            return $events;
        }

        return collect($events)->filter(function ($listeners, $event) use ($eventName) {
            return Str::contains($event, $eventName);
        })->toArray();
    }

    /**
     * Check whether the user is filtering by an event name.
     *
     * @return bool
     */
    protected function filteringByEvent()
    {
        return ! empty($this->option('event'));
    }
}
