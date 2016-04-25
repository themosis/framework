<?php

namespace Themosis\Page;

use Themosis\Foundation\ServiceProvider;

class PageServiceProvider extends ServiceProvider
{
    protected $provides = [
        'page'
    ];

    public function register()
    {
        $data = new PageData();

        $view = $this->getContainer()->get('view');
        $view = $view->make('pages._themosisCorePage');

        $this->getContainer()->add('page', 'Themosis\Page\PageBuilder')->withArguments([
            $data,
            $view,
            $this->getContainer()->get('validation')
        ]);
    }
}
