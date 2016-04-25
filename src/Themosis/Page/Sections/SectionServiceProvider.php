<?php

namespace Themosis\Page\Sections;

use Themosis\Foundation\ServiceProvider;

class SectionServiceProvider extends ServiceProvider
{
    protected $provides = [
        'sections'
    ];

    public function register()
    {
        $data = new SectionData();

        $this->getContainer()->add('sections', 'Themosis\Page\Sections\SectionBuilder')->withArgument($data);
    }
}
