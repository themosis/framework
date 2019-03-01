<?php

namespace Themosis\User;

use Illuminate\Support\ServiceProvider;
use Themosis\Forms\Fields\FieldsRepository;

class UserServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerUserFactory();
        $this->registerUserField();
    }

    /**
     * Register the user factory.
     */
    protected function registerUserFactory()
    {
        $this->app->bind('themosis.user', function ($app) {
            return new Factory($app['validator']);
        });
    }

    /**
     * Register the user field.
     */
    protected function registerUserField()
    {
        $this->app->bind('themosis.user.field', function ($app) {
            $viewFactory = $app['view'];
            $viewFactory->addLocation(__DIR__.'/views');

            return new UserField(
                new FieldsRepository(),
                $app['action'],
                $viewFactory,
                $app['validator']
            );
        });
    }
}
