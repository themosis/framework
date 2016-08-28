<?php

namespace Themosis\Route;

use Themosis\Foundation\ServiceProvider;

class RoutingServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerRouter();
    }

    /**
     * Register the router instance.
     *
     * @return void
     */
    protected function registerRouter()
    {
        $this->app['router'] = $this->app->share(function ($app) {
            return new Router($app['events'], $app);
        });

	    $this->app['wordpress_router'] = $this->app->share(function ($app) {
		    return new WordPressRouter($app);
	    });
    }
}
