<?php

namespace Themosis\Ajax;

use Themosis\Foundation\ServiceProvider;

class AjaxServiceProvider extends ServiceProvider
{
    protected $provides = [
        'ajax'
    ];

    public function register()
    {
        $this->getContainer()->add('ajax', 'Themosis\Ajax\AjaxBuilder')->withArgument('action');
    }

    /**
     * Provide the ajax api instance.
     */
    protected function igniteAjax()
    {
        $this->app->bindShared('ajax', function ($app) {
            /*
             * Create an AjaxBuilder instance.
             */
            return new AjaxBuilder($app['action']);
        });
    }
}
