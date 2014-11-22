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
        $this->app->bindShared('metabox', function($app){

            $data = new MetaboxData();
            $view = View::make('_themosisCoreMetabox');

            $userFactory = $app['user'];
            $user = $userFactory->current();

            return new MetaboxBuilder($data, $view, $app['validation'], $user);

        });
    }
}