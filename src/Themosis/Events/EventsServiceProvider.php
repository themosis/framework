<?php

namespace Themosis\Events;

use Illuminate\Events\Dispatcher;
use Themosis\Foundation\ServiceProvider;

class EventsServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('events', function ($app) {
            return (new Dispatcher($app))->setQueueResolver(function () use ($app) {
                return $app->make('Illuminate\Contracts\Queue\Factory');
            });
        });
    }
}