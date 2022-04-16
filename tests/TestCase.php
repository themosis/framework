<?php

namespace Themosis\Tests;

use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use PHPUnit\Framework\TestCase as PhpUnitTestCase;

class TestCase extends PhpUnitTestCase
{
    protected Application $app;

    protected function setUp(): void
    {
        $this->setApplication();

        parent::setUp();
    }

    private function setApplication(): void
    {
        $app = new Application();

        $app->bind('config', fn () => new Repository());

        $this->app = $app;
    }
}
