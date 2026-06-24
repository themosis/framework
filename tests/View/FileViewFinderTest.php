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

    public function testFindLocatesViewInRegisteredPath()
    {
        $dir = sys_get_temp_dir() . '/themosis_views_' . uniqid();
        mkdir($dir, 0755, true);
        file_put_contents("{$dir}/hello.blade.php", '');

        try {
            $finder = new FileViewFinder(new Filesystem(), [$dir]);
            $path = $finder->find('hello');

            $this->assertStringContainsString('hello.blade.php', $path);
        } finally {
            unlink("{$dir}/hello.blade.php");
            rmdir($dir);
        }
    }

    public function testFindReturnsNamespacedViewFromRegisteredNamespace()
    {
        $dir = sys_get_temp_dir() . '/themosis_views_' . uniqid();
        mkdir($dir, 0755, true);
        file_put_contents("{$dir}/greeting.blade.php", '');

        try {
            $finder = new FileViewFinder(new Filesystem(), []);
            $finder->addNamespace('vendor', $dir);
            $path = $finder->find('vendor::greeting');

            $this->assertStringContainsString('greeting.blade.php', $path);
        } finally {
            unlink("{$dir}/greeting.blade.php");
            rmdir($dir);
        }
    }
}
