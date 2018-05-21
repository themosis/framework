<?php

namespace Themosis\Forms;

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
     * @param string $name
     * @param string $type
     * @param array  $options
     *
     * @return FormBuilderInterface
     */
    public function add(string $name, string $type, array $options = []): FormBuilderInterface
    {
        // Create and add the field to the form instance.
        $field = new $type($name);
        $this->form->addField($field);

        return $this;
    }

    public function get(): FormInterface
    {
        // Build the form and return it.
        $form = new Form();

        return $form;
    }
}
