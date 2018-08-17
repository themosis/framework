<?php

namespace Themosis\Tests\Metabox;

use PHPUnit\Framework\TestCase;
use Themosis\Metabox\Factory;
use Themosis\Metabox\MetaboxInterface;

class MetaboxTest extends TestCase
{
    public function getFactory()
    {
        return new Factory();
    }

    public function testCreateEmptyMetabox()
    {
        $factory = $this->getFactory();

        $box = $factory->make('properties');

        $this->assertInstanceOf(MetaboxInterface::class, $box);
        $this->assertEquals('properties', $box->getId());
        $this->assertEquals('Properties', $box->getTitle());
        $this->assertEquals('post', $box->getScreen());
        $this->assertEquals('advanced', $box->getContext());
        $this->assertEquals('default', $box->getPriority());
    }
}
