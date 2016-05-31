<?php

namespace Themosis\Page\Sections;

use Themosis\Foundation\ServiceProvider;

class SectionServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('sections', function () {

            $data = new SectionData();

            return new SectionBuilder($data);
        });
    }
}
