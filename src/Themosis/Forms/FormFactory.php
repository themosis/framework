<?php

namespace Themosis\Forms;

use Illuminate\Contracts\Validation\Factory as ValidationFactoryInterface;
use Themosis\Forms\Contracts\FormBuilderInterface;
use Themosis\Forms\Contracts\FormFactoryInterface;

class FormFactory implements FormFactoryInterface
{
    /**
     * @var ValidationFactoryInterface
     */
    protected $validation;

    /**
     * Form generator/builder.
     *
     * @var FormBuilderInterface
     */
    protected $builder;

    public function __construct(ValidationFactoryInterface $validation)
    {
        $this->validation = $validation;
    }

    /**
     * Creates a new form instance and returns it.
     *
     * @param mixed  $data    The POPO (DTO) object.
     * @param string $builder
     *
     * @return FormBuilderInterface
     */
    public function make($data = null, $builder = FormBuilder::class)
    {
        $this->builder = new $builder(new Form(new FormRepository(), $this->validation));

        return $this->builder;
    }
}
