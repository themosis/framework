<?php

namespace Themosis\Page;

use Themosis\Foundation\ServiceProvider;

class PageServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('page', function ($container) {

            $data = new PageData();

            $view = $container['view'];
            $view = $view->make('pages._themosisCorePage');

            return new PageBuilder($data, $view, $container['validation'], $container['action']);
        });
    }
}
