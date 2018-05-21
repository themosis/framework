<?php

namespace Themosis\Forms;

use Themosis\Forms\Contracts\FormBuilderInterface;
use Themosis\Forms\Contracts\FormFactoryInterface;

class FormFactory implements FormFactoryInterface
{
    /**
     * Form generator/builder.
     *
     * @var FormBuilderInterface
     */
    protected $builder;

    /**
     * @var FormCollection
     */
    protected $collection;

    /**
     * Creates a new form instance and returns it.
     *
     * @param mixed  $data    The POPO (DTO) object.
     * @param string $builder
     *
     * @return FormBuilderInterface
     */
    public function make($data, $builder = FormBuilder::class)
    {
        $this->builder = new $builder(new Form());

        return $this->builder;
    }
}
