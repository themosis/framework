<?php

namespace Themosis\Forms\Contracts;

use Themosis\Field\Contracts\FieldFactoryInterface;

interface Formidable
{
    /**
     * Build and configure a re-usable form.
     *
     * @param FormFactoryInterface  $factory
     * @param FieldFactoryInterface $fields
     *
     * @return FormInterface
     */
    public function build(FormFactoryInterface $factory, FieldFactoryInterface $fields): FormInterface;
}
