<?php

namespace Themosis\Forms;

use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Forms\Contracts\FormBuilderInterface;
use Themosis\Forms\Contracts\FormInterface;

class FormBuilder implements FormBuilderInterface
{
    /**
     * @var FormInterface
     */
    protected $form;

    public function __construct(FormInterface $form)
    {
        $this->form = $form;
    }

    /**
     * Add a field to the current form instance.
     *
     * @param FieldTypeInterface $field
     * @param array              $options
     *
     * @return FormBuilderInterface
     */
    public function add(FieldTypeInterface $field, array $options = []): FormBuilderInterface
    {
        $field->setOptions($options);

        $this->form->addField($field);

        return $this;
    }

    /**
     * Return generated form instance.
     *
     * @return FormInterface
     */
    public function get(): FormInterface
    {
        return $this->form;
    }
}
