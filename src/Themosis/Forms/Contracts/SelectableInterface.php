<?php

namespace Themosis\Forms\Contracts;

interface SelectableInterface
{
    /**
     * Verify a value against a choice and return
     * a "selected" HTML attribute.
     *
     * @param callable $callback
     * @param array    $args
     *
     * @return string
     */
    public function selected(callable $callback, array $args): string;
}
