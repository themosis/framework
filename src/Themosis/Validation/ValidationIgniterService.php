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
        $this->app->bind('validation', function($app){

            return new ValidationBuilder();

        });
    }
}