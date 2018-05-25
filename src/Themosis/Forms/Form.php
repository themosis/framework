<?php

namespace Themosis\Forms;

use Illuminate\Contracts\Support\MessageBag;
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
         * @todo Implement "attributes"
         * $this->getValidationFactory()
         * ->make($request->all(), $rules, $messages, $customAttributes)
         * ->validate();
         */
        $fields = $this->repository->all();

        $this->validator = $this->validation->make(
            $request->all(),
            $this->getFormRules($fields),
            $this->getFormMessages($fields),
            $this->getFormPlaceholders($fields)
        );

        $this->validator->validate();

        return $this;
    }

    /**
     * Get the list of form rules.
     *
     * @param array $fields The form fields instances.
     *
     * @return array
     */
    protected function getFormRules(array $fields)
    {
        $rules = [];

        foreach ($fields as $field) {
            /** @var FieldTypeInterface $field */
            $rules[$field->getName()] = $field->getOptions('rules');
        }

        return $rules;
    }

    /**
     * Get the list of form fields messages.
     *
     * @param array $fields The form fields instances.
     *
     * @return array
     */
    protected function getFormMessages(array $fields)
    {
        // Each message is defined by field and its own rules.
        // In our case, we need to prepend the field name (attribute)
        // using a "dot" notation. Ex.: email.required
        $messages = [];

        foreach ($fields as $field) {
            /** @var FieldTypeInterface $field */
            foreach ($field->getOptions('messages') as $attr => $message) {
                $messages[$field->getName().'.'.$attr] = $message;
            }
        }

        return $messages;
    }

    /**
     * Get the list of custom :attribute placeholders values.
     *
     * @param array $fields The form fields instances.
     *
     * @return array
     */
    protected function getFormPlaceholders(array $fields)
    {
        $attributes = [];

        foreach ($fields as $field) {
            /** @var FieldTypeInterface $field */
            $attributes[$field->getName()] = $field->getOptions('placeholder');
        }

        return $attributes;
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

    /**
     * Return a list of form errors.
     *
     * @return MessageBag
     */
    public function errors(): MessageBag
    {
        return $this->validator->errors();
    }

    /**
     * Return error messages for a specific field.
     * By setting the second parameter to true, a user
     * can fetch the first error message only on the
     * mentioned field.
     *
     * @param string $name
     * @param bool   $first
     *
     * @return mixed
     */
    public function error(string $name, bool $first = false)
    {
        $errors = $this->errors();

        $field = $this->repository->getFieldByName($name);

        if ($first) {
            return $errors->first($field->getName());
        }

        return $errors->get($field->getName());
    }
}
