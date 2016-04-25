<?php

namespace Themosis\User;

use Themosis\Foundation\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    protected $provides = [
        'user'
    ];

    public function register()
    {
        $view = $this->getContainer()->get('view');
        $view = $view->make('_themosisUserCore');

        $this->getContainer()->add('user', 'Themosis\User\UserFactory')->withArguments([
            $view,
            'validation'
        ]);
    }
}
