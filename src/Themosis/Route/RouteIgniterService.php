<?php
namespace Themosis\Route;

use Themosis\Core\IgniterService;

class RouteIgniterService extends IgniterService {

    /**
     * Ignite a service.
     *
     * @return void
     */
    public function ignite()
    {
        $this->app->bindShared('router', function($app)
        {
            return new Router($app);
        });
    }
}