<?php

namespace Themosis\Support\Contracts;

interface CanTransform
{
    /**
     * Return a resource as an array.
     */
    public function toArray(): array;
}
