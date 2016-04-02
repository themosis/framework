<?php
namespace Themosis\Page\Sections;

use Themosis\Core\IgniterService;

class SectionIgniterService extends IgniterService{

    /**
     * Ignite a service.
     *
     * @return void
     */
    public function ignite()
    {
        $this->app->bind('sections', function($app)
        {
            $data = new SectionData();
            return new SectionBuilder($data);
        });
    }
}