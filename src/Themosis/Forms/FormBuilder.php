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
     * Parse the "options" used by a field instance.
     *
     * @param array $options
     *
     * @return array
     */
    protected function parseOptions(array $options, FieldTypeInterface $field)
    {
        $allowed = $field->getAllowedOptions();
        $parsed = [];

        foreach ($options as $key => $value) {
            if (! in_array($key, $allowed, true)) {
                throw new \DomainException('The "'.$key.'" option is not allowed on the provided field.');
            }

            $parsed[$key] = $value;
        }

        return $parsed;
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
        $opts = array_merge($field->getDefaultOptions(), $options);
        $field->setOptions($this->parseOptions($opts, $field));

        $this->form->repository()->addField($field);

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
