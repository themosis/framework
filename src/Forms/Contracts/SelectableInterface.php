<?php

namespace Themosis\Forms\Contracts;

interface SelectableInterface
{
    /**
     * Verify a value against a choice and return
     * a "selected" HTML attribute.
     */
    public function selected(callable $callback, array $args): string;
}
