<?php

namespace Themosis\Forms;

use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Forms\Contracts\FormBuilderInterface;
use Themosis\Forms\Contracts\FormInterface;
use Themosis\Forms\DataMappers\DataMapperManager;
use Themosis\Forms\Fields\Types\BaseType;
use Themosis\Support\Section;

class FormBuilder implements FormBuilderInterface
{
    use FormHelper;

    /**
     * @var FormInterface|FieldTypeInterface
     */
    protected $form;

    /**
     * @var DataMapperManager
     */
    protected $dataMapperManager;

    /**
     * DTO instance
     *
     * @var mixed
     */
    protected $dataClass;

    public function __construct(FormInterface $form, DataMapperManager $dataMapperManager, $dataClass = null)
    {
        $this->form = $form;
        $this->dataMapperManager = $dataMapperManager;
        $this->dataClass = $dataClass;
    }

    /**
     * Validate the "options" used by a field instance.
     *
     * @param array $options
     *
     * @return array
     */
    protected function validateOptions(array $options, FieldTypeInterface $field)
    {
        $parsed = [];

        foreach ($options as $key => $value) {
            if (! in_array($key, $field->getAllowedOptions(), true)) {
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
     *
     * @return FormBuilderInterface
     */
    public function add(FieldTypeInterface $field): FormBuilderInterface
    {
        /** @var BaseType $field */
        $opts = $this->validateOptions(array_merge([
            'errors' => $this->form->getOption('errors'),
            'theme' => $this->form->getOption('theme')
        ], $field->getOptions()), $field);
        $field->setLocale($this->form->getLocale());
        $field->setOptions($opts);
        $field->setForm($this->form);
        $field->setViewFactory($this->form->getViewer());
        $field->setResourceTransformerFactory($this->form->getResourceTransformerFactory());

        // DTO
        if (! is_null($this->dataClass) && is_object($this->dataClass) && $field->getOption('mapped')) {
            $this->dataMapperManager->mapFromObjectToField($this->dataClass, $field);
        }

        // Check if section instance already exists on the form.
        // If not, create a new section instance.
        if ($this->form->repository()->hasGroup($field->getOption('group'))) {
            // The section/group instance is already registered, just fetch it.
            $section = $this->form->repository()->getGroup($field->getOption('group'));
        } else {
            // No defined group. Let's create an instance so we can attach
            // the field to it right after.
            $section = new Section($field->getOption('group'));
        }

        // Setup group/section default view.
        $section->setTheme($this->form->getTheme());
        $section->setView('form.group');

        // Add the field first to section instance.
        // Then pass both objects to the repository.
        $section->addItem($field);
        $this->form->repository()->addField($field, $section);

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
