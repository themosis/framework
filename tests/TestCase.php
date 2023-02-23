<?php

namespace Themosis\Tests;

use Themosis\Tests\Installers\WordPressConfiguration;
use Themosis\Tests\Installers\WordPressInstaller;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->bind(WordPressConfiguration::class, function () {
            return WordPressInstaller::make()->configuration();
        });
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        WordPressInstaller::make()->refresh();
    }

    public static function applicationBasePath(): string
    {
        return __DIR__ . '/application';
    }
}
