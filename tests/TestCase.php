<?php

namespace Themosis\Tests;

use Illuminate\Config\Repository;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Foundation\Application;
use PDO;
use PHPUnit\Framework\TestCase as PhpUnitTestCase;

class TestCase extends PhpUnitTestCase
{
    protected Application $app;

    protected Manager $database;

    protected function setUp(): void
    {
        $this->setApplication();

        $db = new Manager();

        $db->addConnection([
            'driver' => 'mysql',
            'host' => DB_HOST,
            'port' => '3306',
            'database' => DB_NAME,
            'username' => DB_USER,
            'password' => DB_PASSWORD,
            'unix_socket' => '',
            'charset' => DB_CHARSET,
            'collation' => DB_COLLATE,
            'prefix' => 'wp_',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ]);

        $db->setAsGlobal();

        $this->database = $db;
    }

    protected function tearDown(): void
    {
        // @TODO Drop all tables...
    }

    private function setApplication(): void
    {
        $app = new Application();

        $app->bind('config', fn () => new Repository());

        $this->app = $app;
    }
}
