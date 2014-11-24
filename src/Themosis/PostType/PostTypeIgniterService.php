<?php
namespace Themosis\PostType;

use Themosis\Core\IgniterService;

class PostTypeIgniterService extends IgniterService{

    /**
     * Ignite a service.
     *
     * @return void
     */
    public function ignite()
    {
        $this->app->bind('posttype', function($app){

            $data = new PostTypeData();

            return new PostTypeBuilder($data);

        });
    }
}