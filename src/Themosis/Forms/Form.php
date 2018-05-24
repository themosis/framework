<?php

namespace Themosis\Forms;

use Illuminate\Contracts\Validation\Factory as ValidationFactoryInterface;
use Illuminate\Http\Request;
use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Forms\Contracts\FormInterface;
use Themosis\Forms\Contracts\FormRepositoryInterface;

/**
 * Class Form
 *
 * @package Themosis\Forms
 */
class Form implements FormInterface
{
    /**
     * @var string
     */
    protected $prefix = 'th_';

    /**
     * Form groups.
     *
     * @var array
     */
    protected $groups = [];

    /**
     * @var FormRepositoryInterface
     */
    protected $repository;

    /**
     * @var ValidationFactoryInterface
     */
    protected $validation;

    /**
     * @var \Illuminate\Validation\Validator
     */
    protected $validator;

    public function __construct(FormRepositoryInterface $repository, ValidationFactoryInterface $validation)
    {
        $this->repository = $repository;
        $this->validation = $validation;
    }

    /**
     * Get the form repository instance.
     *
     * @return FormRepositoryInterface
     */
    public function repository(): FormRepositoryInterface
    {
        return $this->repository;
    }

    /**
     * Render a form and returns its HTML structure.
     *
     * @return string
     */
    public function render(): string
    {
        return '';
    }

    /**
     * Set the form prefix. If fields are attached to the form,
     * all fields are updated with the given prefix.
     *
     * @param string $prefix
     *
     * @return FormInterface
     */
    public function setPrefix(string $prefix): FormInterface
    {
        $this->prefix = $prefix;

        // Update all attached fields with the given prefix.
        foreach ($this->repository->all() as $field) {
            /** @var FieldTypeInterface $field */
            $field->setPrefix($prefix);
        }

        return $this;
    }

    /**
     * Return the form prefix.
     *
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * Handle current request and start form data validation.
     *
     * @param Request $request
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * @return $this
     */
    public function handleRequest(Request $request): FormInterface
    {
        /**
         * @todo Implement "messages" and "attributes"
         * $this->getValidationFactory()
         * ->make($request->all(), $rules, $messages, $customAttributes)
         * ->validate();
         */
        $this->validator = $this->validation->make($request->all(), $this->getFormRules());

        $this->validator->validate();

        return $this;
    }

    /**
     * Get the list of form rules.
     *
     * @return array
     */
    protected function getFormRules()
    {
        $rules = [];

        $fields = $this->repository->all();

        foreach ($fields as $field) {
            /** @var FieldTypeInterface $field */
            $rules[$field->getName()] = $field->getOptions('rules');
        }

        return $rules;
    }

    /**
     * Check if submitted form is valid or not.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->validator->passes();
    }
}
