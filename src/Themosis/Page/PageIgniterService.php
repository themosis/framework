<?php
namespace Themosis\Page;

use Themosis\Core\IgniterService;
use Themosis\Core\WrapperView;

class PageIgniterService extends IgniterService {

    /**
     * Ignite a service.
     *
     * @return void
     */
    public function ignite()
    {
        $this->app->bind('page', function($app){

            $data = new PageData();
            $view = new WrapperView(themosis_path('sys').'Page'.DS.'Views'.DS.'default.php');

            return new PageBuilder($data, $view, $app['validation']);

        });
    }
}