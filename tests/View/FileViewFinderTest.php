<?php

namespace Themosis\Tests\View;

use Illuminate\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Themosis\View\FileViewFinder;

class FileViewFinderTest extends TestCase
{
    public function test_add_view_location_with_priority()
    {
        $finder = new FileViewFinder(new Filesystem(), []);

        $finder->addOrderedLocation('first/resources', 1);
        $finder->addLocation('root/resources');
        $finder->prependLocation('plugin/resources');
        $finder->addOrderedLocation('child/resources', 10);
        $finder->prependLocation('theme/resources');

        $this->assertEquals('first/resources', $finder->getPaths()[0]);
        $this->assertEquals('child/resources', $finder->getPaths()[1]);
    }
}
