<?php
namespace Themosis\Filter;

use Themosis\Core\IgniterService;

class FilterIgniterService extends IgniterService
{
    /**
     * Make the Filter API classes available.
     *
     * @return void
     */
    public function ignite()
    {
        $this->igniteFilter();
    }

    /**
     * Build an instance of the Filter API.
     *
     * @return \Themosis\Filter\FilterBuilder
     */
    protected function igniteFilter()
    {
        $this->app->bindShared('filter', function($app)
        {
            /**
             * This creates an FilterBuilder instance with its dependencies.
             */
            return new FilterBuilder($app);
        });
    }
}
