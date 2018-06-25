<?php

namespace Themosis\Forms\Transformers;

use Themosis\Forms\Contracts\DataTransformerInterface;

class ChoiceToValueTransformer implements DataTransformerInterface
{
    /**
     * @inheritdoc
     *
     * @param string|array $data
     *
     * @return string|array
     */
    public function transform($data)
    {
        return $data;
    }

    /**
     * @inheritdoc
     *
     * @param string|array $data
     *
     * @return string|array
     */
    public function reverseTransform($data)
    {
        return $data;
    }
}
