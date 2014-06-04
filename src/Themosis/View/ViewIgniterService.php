<?php
namespace Themosis\View;

use Themosis\Core\IgniterService;

class ViewIgniterService extends IgniterService{

    /**
     * Ignite a service.
     *
     * @return void
     */
    public function ignite()
    {
        $this->app->bind('view', function($app){

            return new ViewFactory();

        });
    }
}