<?php
namespace Themosis\Html;

use Themosis\Core\IgniterService;

class FormIgniterService extends IgniterService
{
    /**
     * Make available the FormBuilder class
     * to the global class.
     *
     * @return void
     */
    public function ignite()
    {
        $this->igniteForm();
    }

    /**
     * Instantiate the FormBuilder class
     * and make it available to the global class.
     *
     * @return \Themosis\Html\FormBuilder
     */
    protected function igniteForm()
    {
        // The '$app' variable use the main Application instance
        // when the closure is called.
        $this->app->bindShared('form', function($app){

            /**
             * This create a FormBuilder instance with its dependencies.
             */
            return new FormBuilder($app['html'], $app['request']);

        });
    }

} 