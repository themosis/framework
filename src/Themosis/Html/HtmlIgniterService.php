<?php
namespace Themosis\Html;

use Themosis\Core\IgniterService;

class HtmlIgniterService extends IgniterService{

    /**
     * Ignite a service.
     *
     * @return void
     */
    public function ignite()
    {
        $this->igniteHtml();
    }

    /**
     * Instantiate the HtmlBuilder class
     * and make it available to the global class.
     *
     * @return \Themosis\Html\HtmlBuilder
     */
    protected function igniteHtml()
    {
        // The '$app' variable use the main Application instance
        // when the closure is called.
        $this->app->bindShared('html', function($app){

            /**
             * This creates a HtmlBuilder instance.
             */
            return new HtmlBuilder();

        });
    }
}