<?php

namespace Themosis\Forms;

use Illuminate\Contracts\Validation\Factory as ValidationFactoryInterface;
use Illuminate\Contracts\View\Factory as ViewFactoryInterface;
use Themosis\Forms\Contracts\FormBuilderInterface;
use Themosis\Forms\Contracts\FormFactoryInterface;

class FormFactory implements FormFactoryInterface
{
    /**
     * @var ValidationFactoryInterface
     */
    protected $validation;

    /**
     * @var ViewFactoryInterface
     */
    protected $viewer;

    /**
     * Form generator/builder.
     *
     * @var FormBuilderInterface
     */
    protected $builder;

    public function __construct(ValidationFactoryInterface $validation, ViewFactoryInterface $viewer)
    {
        $this->validation = $validation;
        $this->viewer = $viewer;
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
        $this->builder = new $builder(new Form(new FormRepository(), $this->validation, $this->viewer));

        return $this->builder;
    }
}
