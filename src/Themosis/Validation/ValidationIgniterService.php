<?php
namespace Themosis\Validation;

use Themosis\Core\IgniterService;

class ValidationIgniterService extends IgniterService {

    /**
     * Ignite a service.
     *
     * @return void
     */
    public function ignite()
    {
        $this->app->bindShared('validation', function($app)
        {
            return new ValidationBuilder();
        });
    }
}