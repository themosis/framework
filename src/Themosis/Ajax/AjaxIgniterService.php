<?php
namespace Themosis\Ajax;

use Themosis\Core\IgniterService;

class AjaxIgniterService extends IgniterService
{
    /**
     * Ignite the ajax service.
     *
     * @return void
     */
    public function ignite()
    {
        $this->igniteAjax();
    }

    /**
     * Provide the ajax api instance.
     *
     * @return void
     */
    protected function igniteAjax()
    {
        $this->app->bindShared('ajax', function($app)
        {
            /**
             * Create an AjaxBuilder instance.
             */
            return new AjaxBuilder($app['action']);
        });
    }
}