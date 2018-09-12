<?php

namespace Themosis\Support\Contracts;

interface CanTransform
{
    /**
     * Return a resource as an array.
     *
     * @return array
     */
    public function toArray(): array;
}
