<?php
namespace Themosis\Metabox;

use Themosis\Core\IgniterService;
use Themosis\Core\WrapperView;

class MetaboxIgniterService extends IgniterService {

    /**
     * Ignite a service.
     *
     * @return void
     */
    public function ignite()
    {
        $this->app->bind('metabox', function($app){

            $datas = new MetaboxData();
            $view = new WrapperView(themosis_path('sys').'Metabox'.DS.'Views'.DS.'default.php');

            return new MetaboxBuilder($datas, $view, $app['validation']);

        });
    }
}