<?php

namespace Themosis\Tests\Core;

use PHPUnit\Framework\TestCase;
use Themosis\Core\AliasLoader;

class AliasLoaderTest extends TestCase
{
    public function testLoaderCanBeCreatedAndRegisteredOnce()
    {
        $loader = AliasLoader::getInstance(['foo' => 'bar']);

        $this->assertEquals(['foo' => 'bar'], $loader->getAliases());
        $this->assertFalse($loader->isRegistered());

        $loader->register();
        $this->assertTrue($loader->isRegistered());
    }

    public function testGetInstanceCreateOneInstance()
    {
        $loader = AliasLoader::getInstance(['some' => 'thing']);
        $this->assertSame($loader, AliasLoader::getInstance());
    }

    public function testAliasAreRegisteredAfterInstantiation()
    {
        $loader = AliasLoader::getInstance(['bar' => 'baz']);
        $loader->alias('foo', 'doe');

        // Still same instance on all tests.
        $this->assertEquals([
            'bar' => 'baz',
            'foo' => 'doe',
            'some' => 'thing'
        ], $loader->getAliases());
    }
}
