<?php
namespace Themosis\Metabox;

use Themosis\Core\IgniterService;
use Themosis\Facades\View;

class MetaboxIgniterService extends IgniterService {

    /**
     * Ignite a service.
     *
     * @return void
     */
    public function ignite()
    {
        $this->app->bind('metabox', function($app){

            $data = new MetaboxData();
            $view = View::make('_themosisCoreMetabox');

            return new MetaboxBuilder($data, $view, $app['validation']);

        });
    }
}