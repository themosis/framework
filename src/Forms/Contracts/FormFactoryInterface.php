<?php

namespace Themosis\Forms\Contracts;

use Themosis\Field\Fields\FieldBuilder;

interface FormFactoryInterface
{
    /**
     * Create a FormBuilderInterface instance.
     *
     * @param mixed  $data    Data object (DTO).
     * @param array  $options
     * @param string $builder A FormBuilderInterface class.
     *
     * @return FormBuilderInterface
     */
    public function make($data = null, $options = [], $builder = FieldBuilder::class): FormBuilderInterface;
}
