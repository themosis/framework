<?php
namespace Themosis\User;

use Themosis\Core\IgniterService;

class UserIgniterService extends IgniterService {

    /**
     * Ignite a service.
     *
     * @return void
     */
    public function ignite()
    {
        $this->app->bind('user', function($app){

            $factory = new UserFactory;

            // Register a User instance for the administrator user.
            $factory->add(1);

            return $factory;

        });
    }
}