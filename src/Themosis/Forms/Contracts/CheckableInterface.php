<?php

namespace Themosis\Forms\Contracts;

interface CheckableInterface
{
    /**
     * Verify a value against a choice and return
     * a "checked" HTML attribute.
     *
     * @param callable $callback
     * @param array    $args
     *
     * @return string
     */
    public function checked(callable $callback, array $args): string;
}
