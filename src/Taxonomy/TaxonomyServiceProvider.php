<?php

namespace Themosis\Taxonomy;

use Illuminate\Support\ServiceProvider;

class TaxonomyServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerTaxonomyFactory();
        $this->registerTaxonomyField();
    }

    /**
     * Register taxonomy factory.
     */
    protected function registerTaxonomyFactory()
    {
        $this->app->bind('taxonomy', function ($app) {
            return new Factory($app, $app['action']);
        });
    }

    /**
     * Register taxonomy field factory.
     */
    protected function registerTaxonomyField()
    {
        $this->app->bind('taxonomy.field', function ($app) {
            $viewFactory = $app['view'];
            $viewFactory->addLocation(__DIR__.'/views');

            return new TaxonomyFieldFactory(
                $viewFactory,
                $app['validator'],
                $app['action']
            );
        });
    }
}
