<?php

namespace Themosis\PostType;

use Themosis\Foundation\ServiceProvider;

class PostTypeServiceProvider extends ServiceProvider
{
    protected $provides = [
        'posttype'
    ];

    public function register()
    {
        $data = new PostTypeData();

        $view = $this->getContainer()->get('view');
        $view = $view->make('_themosisCorePublishBox');

        $this->getContainer()->add('posttype', 'Themosis\PostType\PostTypeBuilder')->withArguments([
            $data,
            $this->getContainer()->get('metabox'),
            $view
        ]);
    }
}
