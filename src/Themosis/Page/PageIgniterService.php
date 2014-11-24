<?php
namespace Themosis\Page;

use Themosis\Core\IgniterService;
use Themosis\Facades\View;

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
            $view = View::make('pages._themosisCorePage');

            return new PageBuilder($data, $view, $app['validation']);

        });
    }
}