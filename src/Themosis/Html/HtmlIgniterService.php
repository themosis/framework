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
        $this->app->bind('html', function($app){

            return new HtmlBuilder();

        });
    }
}