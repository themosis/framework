<?php

namespace Themosis\Schema\Contracts;

interface TransformerInterface
{
    /**
     * Transform given item and return the results as an array.
     *
     * @param object $item
     *
     * @return array
     */
    public function transform($item): array;
}
