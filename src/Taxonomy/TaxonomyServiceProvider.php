<?php

namespace Themosis\Taxonomy;

use Illuminate\Support\ServiceProvider;

class TaxonomyServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('taxonomy', function ($app) {
            return new Factory($app, $app['action']);
        });
    }
}
