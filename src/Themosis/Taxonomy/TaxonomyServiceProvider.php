<?php

namespace Themosis\Taxonomy;

use Themosis\Foundation\ServiceProvider;

class TaxonomyServiceProvider extends ServiceProvider
{
    protected $provides = [
        'taxonomy'
    ];

    public function register()
    {
        $data = new TaxonomyData();

        $this->getContainer()->add('taxonomy', 'Themosis\Taxonomy\TaxonomyBuilder')->withArgument($data);
    }
}
