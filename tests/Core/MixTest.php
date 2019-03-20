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
        if (! is_null($this->app)) {
            return $this->app;
        }

        return $this->app = new Application(dirname(__DIR__));
    }

    public function testDefaultMixUsage()
    {
        $this->getApplication();

        $mix = app(Mix::class);
        $this->assertEquals(
            $mix('dist/css/theme.css'),
            get_home_url(null, 'content/themes/underscore/dist/css/theme.css?id=ba9aead02aea5cc7befb')
        );
    }

    public function testSpecifyingDirectory()
    {
        $this->getApplication();

        $mix = app(Mix::class);
        $this->assertEquals(
            $mix('dist/css/theme.css', 'content/themes/underscore'),
            get_home_url(null, 'content/themes/underscore/dist/css/theme.css?id=ba9aead02aea5cc7befb')
        );
    }

    public function testCallingFromPlugin()
    {
        $this->getApplication();

        $mix = app(Mix::class);
        $this->assertEquals(
            $mix('dist/css/theme.css', 'content/plugins/timeline'),
            get_home_url(null, 'content/plugins/timeline/dist/css/theme.css?id=ba9aead02aea5cc7befb')
        );
    }

    public function testCallingFromWebRoot()
    {
        $this->getApplication();

        $mix = app(Mix::class);
        $this->assertEquals(
            $mix('dist/css/theme.css', '/'),
            get_home_url(null, '/dist/css/theme.css?id=ba9aead02aea5cc7befb')
        );
    }
}
