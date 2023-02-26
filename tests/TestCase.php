<?php

namespace Themosis\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Themosis\Foundation\Installers\WordPressConfiguration;

class TestCase extends BaseTestCase
{
    protected function defineEnvironment($app): void
    {
        $wordpressConfiguration = WordPressConfiguration::make();

        $app['config']->set('database.default', 'themosis');
        $app['config']->set('database.connections.themosis', [
            'driver' => 'mysql',
            'database' => $wordpressConfiguration->databaseName(),
            'host' => $wordpressConfiguration->databaseHost(),
            'username' => $wordpressConfiguration->databaseUser(),
            'password' => $wordpressConfiguration->databasePassword(),
            'prefix' => $wordpressConfiguration->tablePrefix(),
        ]);
    }

    public static function applicationBasePath(): string
    {
        return THEMOSIS_ROOT;
    }
}
