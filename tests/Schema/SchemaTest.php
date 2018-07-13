<?php

namespace Themosis\Tests\Schema;

use PHPUnit\Framework\TestCase;
use Themosis\Schema\Resource;
use Themosis\Tests\Schema\Transformers\BasicTransformer;

class SchemaTest extends TestCase
{
    private function getGenericObject()
    {
        $instance = new \stdClass();
        $instance->name = 'Product Name';
        $instance->description = 'Some product information';
        $instance->tags = ['beginner', 'public'];
        $instance->price = 20;

        return $instance;
    }

    public function testBasicSchema()
    {
        $resource = new Resource();
        $resource->using($this->getGenericObject())
            ->transformWith(new BasicTransformer());

        $expected = [
            'properties' => [
                'name' => [
                    'type' => 'string'
                ],
                'description' => [
                    'type' => 'string'
                ],
                'tags' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'string'
                    ]
                ],
                'price' => [
                    'type' => 'number'
                ]
            ]
        ];

        $this->assertEquals($expected, $resource->get());
    }
}
