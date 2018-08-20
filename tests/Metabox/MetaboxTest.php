<?php

namespace Themosis\Tests\Metabox;

use PHPUnit\Framework\TestCase;
use Themosis\Core\Application;
use Themosis\Hook\ActionBuilder;
use Themosis\Metabox\Factory;
use Themosis\Metabox\MetaboxInterface;

class MetaboxTest extends TestCase
{
    public function getFactory()
    {
        $app = new Application();

        return new Factory(
            $app,
            new ActionBuilder($app)
        );
    }

    public function testCreateEmptyMetaboxWithDefaultArguments()
    {
        $factory = $this->getFactory();

        $box = $factory->make('properties');

        $this->assertInstanceOf(MetaboxInterface::class, $box);
        $this->assertEquals('properties', $box->getId());
        $this->assertEquals('Properties', $box->getTitle());
        $this->assertEquals('post', $box->getScreen());
        $this->assertEquals('advanced', $box->getContext());
        $this->assertEquals('default', $box->getPriority());
        $this->assertEquals([$box, 'handle'], $box->getCallback());
        $this->assertTrue(is_array($box->getArguments()));
        $this->assertTrue(empty($box->getArguments()));
        $this->assertEquals('default', $box->getLayout());
    }

    public function testCreateMetaboxResource()
    {
        $factory = $this->getFactory();

        $box = $factory->make('infos');

        $this->assertEquals([], $box->toArray());
    }
}
