<?php

namespace Themosis\Forms\Contracts;

use Themosis\Field\Contracts\FieldFactoryInterface;

interface Formidable
{
    /**
     * Build and configure a re-usable form.
     */
    public function build(FormFactoryInterface $factory, FieldFactoryInterface $fields): FormInterface;
}
