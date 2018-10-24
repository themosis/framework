<?php

namespace Themosis\Forms\Fields\ChoiceList;

interface ChoiceListInterface
{
    /**
     * Format the choices for use (before output).
     *
     * @return ChoiceListInterface
     */
    public function format(): ChoiceListInterface;

    /**
     * Return formatted choices.
     *
     * @return array
     */
    public function get(): array;
}
