<?php

namespace Themosis\Tests\Core;

use Illuminate\Config\Repository;
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

        $app = new Application(dirname(__DIR__));

        $app->singleton('config', function () {
            return new Repository();
        });

        return $this->app = $app;
    }

    public function testDefaultMixUsage()
    {
        $mix = app(Mix::class);

        $this->assertEquals(
            '/content/themes/underscore/dist/css/theme.css?id=ba9aead02aea5cc7befb',
            $mix('css/theme.css')->toHtml()
        );
    }

    public function testSpecifyingDirectory()
    {
        $mix = app(Mix::class);

        $this->assertEquals(
            '/content/themes/underscore/dist/css/theme.css?id=ba9aead02aea5cc7befb',
            $mix('css/theme.css', 'content/themes/underscore/dist')->toHtml()
        );
    }

    public function testCallingFromPlugin()
    {
        $mix = app(Mix::class);

        $this->assertEquals(
            '/content/plugins/timeline/dist/css/theme.css?id=ba9aead02aea5cc7befb',
            $mix('css/theme.css', 'content/plugins/timeline/dist')->toHtml()
        );
    }

    public function testCallingFromWebRoot()
    {
        $mix = app(Mix::class);

        $this->assertEquals(
            '/css/theme.css?id=ba9aead02aea5cc7befb',
            $mix('css/theme.css', '/')->toHtml()
        );
    }
}
