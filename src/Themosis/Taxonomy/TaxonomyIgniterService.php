<?php
namespace Themosis\Taxonomy;

use Themosis\Core\IgniterService;

class TaxonomyIgniterService extends IgniterService {

    /**
     * Ignite a service.
     *
     * @return void
     */
    public function ignite()
    {
        $this->app->bind('taxonomy', function($app)
        {
            $data = new TaxonomyData();
            return new TaxonomyBuilder($data);
        });
    }
}