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
        $this->app->bind('field', function($app){

            return new FieldFactory();

        });
    }
}