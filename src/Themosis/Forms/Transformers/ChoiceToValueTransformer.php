<?php

namespace Themosis\Forms\Transformers;

use Themosis\Forms\Contracts\DataTransformerInterface;

class ChoiceToValueTransformer implements DataTransformerInterface
{
    public function transform($data)
    {
        return $data;
    }

    public function reverseTransform($data)
    {
        return $data;
    }
}
