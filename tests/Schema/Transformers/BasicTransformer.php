<?php

namespace Themosis\Tests\Schema\Transformers;

use Themosis\Schema\Contracts\TransformerInterface;

class BasicTransformer implements TransformerInterface
{
    public function transform($item): array
    {
        return [
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
    }
}
