<?php

namespace Themosis\Tests;

use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use PHPUnit\Framework\TestCase as PhpUnitTestCase;
use Themosis\Tests\Installers\WordPressConfiguration;
use Themosis\Tests\Installers\WordPressInstaller;

class TestCase extends PhpUnitTestCase
{
    protected Application $app;

    protected function setUp(): void
    {
        $this->setApplication();
    }

    protected function tearDown(): void
    {
        WordPressInstaller::make()->refresh();
    }

    private function setApplication(): void
    {
        $app = new Application(dirname(__DIR__));

        $app->instance('config', new Repository());

        $app->bind(WordPressConfiguration::class, function () {
            return WordPressInstaller::make()->configuration();
        });

        $this->app = $app;
    }
}
