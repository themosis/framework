<?php

namespace Themosis\Taxonomy;

use Themosis\Foundation\ServiceProvider;

class TaxonomyServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('taxonomy', function ($container) {

            $data = new TaxonomyData();

            return new TaxonomyBuilder($container, $data, $container['action'], $container['validation'], $container['view']);
        });
    }
}
