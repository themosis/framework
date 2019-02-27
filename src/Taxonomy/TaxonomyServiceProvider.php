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

        $this->app->bind('taxonomy.field', function ($app) {
            $viewFactory = $app['view'];
            $viewFactory->addLocation(__DIR__.'/views');

            return new TaxonomyField(
                new TaxonomyFieldRepository(),
                $viewFactory,
                $app['validator'],
                $app['action']
            );
        });
    }
}
