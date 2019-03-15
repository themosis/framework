<?php

namespace Themosis\Tests\Core;

use PHPUnit\Framework\TestCase;
use Themosis\Core\Application;
use Themosis\Core\Mix;

class MixTest extends TestCase
{
    protected $app;

    protected function getApplication()
    {
        if (! is_null($this->app)) 
            return $this->app;

        return $this->app = new Application();
    }
    public function testDefaultMixUsage()
    {
        $this->getApplication();

        $mix = app(Mix::class);
        $this->assertEquals(
            $mix('dist/css/theme.css'),
            content_url('/themes/underscore/dist/css/theme.css?id=ba9aead02aea5cc7befb')
        );
    }

    public function testSpecifyingDirectory()
    {
        $this->getApplication();

        $mix = app(Mix::class);
        $this->assertEquals(
            $mix('dist/css/theme.css', 'themes/underscore'),
            content_url('/themes/underscore/dist/css/theme.css?id=ba9aead02aea5cc7befb')
        );
    }

    public function testCallingFromPlugin()
    {
        $this->getApplication();

        $mix = app(Mix::class);
        $this->assertEquals(
            $mix('dist/css/theme.css', 'plugins/timeline'),
            content_url('/plugins/timeline/dist/css/theme.css?id=ba9aead02aea5cc7befb')
        );
    }

    public function testCallingFromContentArea()
    {
        $this->getApplication();

        $mix = app(Mix::class);
        $this->assertEquals(
            $mix('dist/css/theme.css', '/'),
            content_url('/dist/css/theme.css?id=ba9aead02aea5cc7befb')
        );
    }
}