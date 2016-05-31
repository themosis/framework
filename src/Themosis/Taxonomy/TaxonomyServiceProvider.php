<?php

namespace Themosis\Taxonomy;

use Themosis\Foundation\ServiceProvider;

class TaxonomyServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('taxonomy', function () {

            $data = new TaxonomyData();

            return new TaxonomyBuilder($data);
        });
    }
}
