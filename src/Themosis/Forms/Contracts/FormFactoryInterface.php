<?php

namespace Themosis\Forms\Contracts;

use Themosis\Field\Fields\FieldBuilder;

interface FormFactoryInterface
{
    /**
     * Create a FormBuilderInterface instance.
     *
     * @param array  $options
     * @param mixed  $data    Data object (DTO).
     * @param string $builder A FormBuilderInterface class.
     *
     * @return FormBuilderInterface
     */
    public function make($options = [], $data = null, $builder = FieldBuilder::class): FormBuilderInterface;
}
