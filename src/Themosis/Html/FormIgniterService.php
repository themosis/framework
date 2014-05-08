<?php
namespace Themosis\Html;

use Themosis\Core\IgniterService;

class FormIgniterService extends IgniterService{

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
        $this->app->bind('form', function(){

            /**
             * @TODO Find a way to get the dependencies for the called class!
             */
            return new FormBuilder();

        });
    }

} 