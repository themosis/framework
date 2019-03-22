<?php

namespace Themosis\Tests\Core;

use PHPUnit\Framework\TestCase;
use Themosis\Core\Application;
use Themosis\Core\Mix;

class MixTest extends TestCase
{
    protected $app;

    public function setUp()
    {
        $this->getApplication();
    }

    protected function getApplication()
    {
        if (! is_null($this->app)) {
            return $this->app;
        }

        return $this->app = new Application(dirname(__DIR__));
    }

    public function testDefaultMixUsage()
    {
        $mix = app(Mix::class);
        $this->assertEquals(
            $mix('css/theme.css'),
            '/css/theme.css?id=ba9aead02aea5cc7befb'
        );
    }

    public function testSpecifyingDirectory()
    {
        $mix = app(Mix::class);
        $this->assertEquals(
            $mix('css/theme.css', 'content/themes/underscore/dist'),
            '/css/theme.css?id=ba9aead02aea5cc7befb'
        );
    }

    public function testCallingFromPlugin()
    {
        $mix = app(Mix::class);
        $this->assertEquals(
            $mix('css/theme.css', 'content/plugins/timeline/dist'),
            '/css/theme.css?id=ba9aead02aea5cc7befb'
        );
    }

    public function testCallingFromWebRoot()
    {
        $mix = app(Mix::class);
        $this->assertEquals(
            $mix('css/theme.css', '/'),
            '/css/theme.css?id=ba9aead02aea5cc7befb'
        );
    }
}
