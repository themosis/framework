<?php

namespace Themosis\Forms\Contracts;

use Themosis\Forms\FormBuilder;

interface FormFactoryInterface
{
    /**
     * Create a FormBuilderInterface instance.
     *
     * @param  mixed  $data    Data object (DTO).
     * @param  array  $options
     * @param  string  $builder A FormBuilderInterface class.
     */
    public function make($data = null, $options = [], $builder = FormBuilder::class): FormBuilderInterface;
}
