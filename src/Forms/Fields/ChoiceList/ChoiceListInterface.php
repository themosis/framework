<?php

namespace Themosis\Forms\Fields\ChoiceList;

interface ChoiceListInterface
{
    /**
     * Format the choices for use (before output).
     */
    public function format(): ChoiceListInterface;

    /**
     * Return formatted choices.
     */
    public function get(): array;
}
