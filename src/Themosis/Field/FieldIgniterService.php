<?php
namespace Themosis\Field;

use Themosis\Core\IgniterService;

class FieldIgniterService extends IgniterService {

    /**
     * Ignite a service.
     *
     * @return void
     */
    public function ignite()
    {
        $this->app->bindShared('field', function($app)
        {
            return new FieldFactory($app['view']);
        });
    }
}