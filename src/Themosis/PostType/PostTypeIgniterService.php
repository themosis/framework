<?php
namespace Themosis\PostType;

use Themosis\Core\IgniterService;
use Themosis\Facades\View;

class PostTypeIgniterService extends IgniterService{

    /**
     * Ignite a service.
     *
     * @return void
     */
    public function ignite()
    {
        $this->app->bind('posttype', function($app)
        {
            $data = new PostTypeData();
            $view = View::make('_themosisCorePublishBox');
            return new PostTypeBuilder($data, $app['metabox'], $view);
        });
    }
}