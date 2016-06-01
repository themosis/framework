<?php

namespace Themosis\User;

use Themosis\Foundation\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('user', function ($container) {

            $view = $container['view'];
            $view = $view->make('_themosisUserCore');

            return new UserFactory($view, $container['validation'], $container['action']);
        });
    }
}
