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
        $this->app->bind('user', function($app)
        {
            // User core view.
            $view = $app['view'];
            $view = $view->make('_themosisUserCore');

            $factory = new UserFactory($view, $app['validation']);
            return $factory;

        });
    }
}