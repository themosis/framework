<?php

namespace Themosis\Metabox;

use Themosis\Foundation\ServiceProvider;

class MetaboxServiceProvider extends ServiceProvider
{
    protected $provides = [
        'metabox'
    ];

    public function register()
    {
        $data = new MetaboxData();

        $view = $this->getContainer()->get('view');
        $view = $view->make('_themosisCoreMetabox');

        $user = $this->getContainer()->get('user');
        $user = $user->current();

        $this->getContainer()->add('metabox', 'Themosis\Metabox\MetaboxBuilder')->withArguments([
            $data,
            $view,
            $this->getContainer()->get('validation'),
            $user
        ]);
    }
}
