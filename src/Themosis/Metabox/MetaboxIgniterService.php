<?php
namespace Themosis\Metabox;

use Themosis\Core\IgniterService;

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
            $view = $app['view'];
            $view = $view->make('_themosisCoreMetabox');

            $userFactory = $app['user'];
            $user = $userFactory->current();

            return new MetaboxBuilder($data, $view, $app['validation'], $user);

        });
    }
}