<?php

namespace Themosis\Forms;

use DomainException;
use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Contracts\Validation\Factory as ValidationFactoryInterface;
use Illuminate\Contracts\View\Factory as ViewFactoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Factory;
use Themosis\Forms\Contracts\DataTransformerInterface;
use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Forms\Contracts\FormInterface;
use Themosis\Forms\Contracts\FormRepositoryInterface;
use Themosis\Forms\Fields\Types\BaseType;
use Themosis\Html\HtmlBuilder;

/**
 * Class Form
 *
 * @package Themosis\Forms
 */
class Form extends HtmlBuilder implements FormInterface, FieldTypeInterface
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

    /**
     * @var ViewFactoryInterface
     */
    protected $viewer;

    /**
     * Form view name.
     *
     * @var string
     */
    protected $view = 'form.default';

    /**
     * Indicates if form is rendered.
     *
     * @var bool
     */
    protected $rendered = false;

    /**
     * Form options.
     *
     * @var array
     */
    protected $options;

    /**
     * List of allowed options on a form instance.
     *
     * @var array
     */
    protected $allowedOptions = [
        'name',
        'attributes',
        'nonce',
        'nonce_action',
        'referer',
        'flush',
        'errors'
    ];

    /**
     * List of default form options.
     *
     * @var array
     */
    protected $defaultOptions = [
        'attributes' => [],
        'flush' => true,
        'errors' => true
    ];

    /**
     * Form "name" attribute value.
     *
     * @var string
     */
    protected $basename;

    /**
     * Form locale (intl).
     *
     * @var string
     */
    protected $locale;

    public function __construct(
        FormRepositoryInterface $repository,
        ValidationFactoryInterface $validation,
        ViewFactoryInterface $viewer
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->validation = $validation;
        $this->viewer = $viewer;

        /** @var Factory $validation */
        $this->setLocale($validation->getTranslator()->getLocale());
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
     * Set the form prefix. If fields are attached to the form,
     * all fields are updated with the given prefix.
     *
     * @param string $prefix
     *
     * @return FieldTypeInterface
     */
    public function setPrefix(string $prefix): FieldTypeInterface
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
        $fields = $this->repository->all();

        $this->validator = $this->validation->make(
            $request->all(),
            $this->getFormRules($fields),
            $this->getFormMessages($fields),
            $this->getFormPlaceholders($fields)
        );

        $data = $this->validator->valid();

        // Attach the errors message bag to each field.
        array_walk($fields, function ($field) use ($data) {
            /** @var $field BaseType */
            $field->setErrorMessageBag($this->errors());

            // By default, if the form is not valid, we keep populating fields values.
            // In the case of a valid form, by default, values are flushed except if
            // the "flush" option for the form has been set to true.
            if ($this->validator->fails() || false === $this->getOptions('flush')) {
                $field->setValue(Arr::get($data, $field->getName()));
            }
        });

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
        if (is_null($this->validator)) {
            return false;
        }

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
     * @return string|array
     */
    public function error(string $name = '', bool $first = false)
    {
        $errors = $this->errors();

        if (empty($name)) {
            // Return all errors messages by default if the
            // name argument is not specified.
            return $errors->all();
        }

        $field = $this->repository->getFieldByName($name);

        if ($first) {
            return $errors->first($field->getName());
        }

        return $errors->get($field->getName());
    }

    /**
     * Render a form and returns its HTML structure.
     *
     * @return string
     */
    public function render(): string
    {
        $view = $this->viewer->make($this->getView(), $this->getFormData());

        // Indicates that the form has been rendered at least once.
        // Then return its content.
        $this->rendered = true;

        return $view->render();
    }

    /**
     * Retrieve form view data.
     *
     * @return array
     */
    protected function getFormData(): array
    {
        // Only provide the form instance
        // to its view under the private
        // variable "$__form".
        return [
            '__form' => $this
        ];
    }

    /**
     * Set form group view file.
     *
     * @param string $view
     * @param string $group
     *
     * @return FormInterface
     */
    public function setGroupView(string $view, string $group = 'default'): FormInterface
    {
        // Verify that the form has the mentioned group.
        // If not, throw an error.
        if (! $this->repository()->hasGroup($group)) {
            throw new DomainException('You cannot change the view of an undefined form group.');
        }

        $this->repository()->getGroup($group)->setView($view);

        return $this;
    }

    /**
     * Specify the view file to use by the form.
     *
     * @param string $view
     *
     * @return FieldTypeInterface
     */
    public function setView(string $view): FieldTypeInterface
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Return the view instance used by the form.
     *
     * @return string
     */
    public function getView(): string
    {
        return $this->view;
    }

    /**
     * Get the view factory instance.
     *
     * @return ViewFactoryInterface
     */
    public function getViewer(): ViewFactoryInterface
    {
        return $this->viewer;
    }

    /**
     * Indicates if the form has been rendered or not.
     *
     * @return bool
     */
    public function isRendered(): bool
    {
        return $this->rendered;
    }

    /**
     * Validate form options.
     *
     * @param array $options
     *
     * @return array
     */
    protected function validateOptions(array $options)
    {
        $validated = [];

        foreach ($options as $name => $option) {
            if (! in_array($name, $this->getAllowedOptions())) {
                throw new DomainException('The "'.$name.'" option is not allowed on the provided form.');
            }

            $validated[$name] = $option;
        }

        return $validated;
    }

    /**
     * Set form options.
     *
     * @param array $options
     *
     * @return FieldTypeInterface
     */
    public function setOptions(array $options): FieldTypeInterface
    {
        $this->validateOptions($options);
        $this->options = $this->parseOptions(array_merge(
            $this->getDefaultOptions(),
            $this->options,
            $options
        ));

        return $this;
    }

    /**
     * Parse form options and add some default parameters.
     *
     * @param array $options
     *
     * @return array
     */
    protected function parseOptions(array $options)
    {
        // Make sure to keep defined default attributes on the form.
        $options['attributes'] = array_merge($this->getAttributes(), $options['attributes']);

        // Define nonce default values if "method" attribute is set to "post".
        if (isset($options['attributes']['method']) && 'post' === strtolower($options['attributes']['method'])) {
            $options['nonce'] = $options['nonce'] ?? '_themosisnonce';
            $options['nonce_action'] = $options['nonce_action'] ?? 'form';
            $options['referer'] = $options['referer'] ?? true;
        }

        return $options;
    }

    /**
     * Return form options.
     *
     * @param string $optionKey
     *
     * @return array
     */
    public function getOptions(string $optionKey = '')
    {
        return $this->options[$optionKey] ?? $this->options;
    }

    /**
     * Get the form "name" attribute value.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->options['name'] ?? '';
    }

    /**
     * getName() method alias.
     *
     * @return string
     */
    public function getBaseName(): string
    {
        return $this->getName();
    }

    /**
     * Return the form attributes.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->getOptions('attributes');
    }

    /**
     * Get the value of a defined attribute.
     *
     * @param string $name The attribute name.
     *
     * @return mixed
     */
    public function getAttribute(string $name)
    {
        $atts = $this->getAttributes();

        return $atts[$name] ?? '';
    }

    /**
     * Set the form attributes.
     *
     * @param array $attributes
     *
     * @return FieldTypeInterface
     */
    public function setAttributes(array $attributes)
    {
        $this->options['attributes'] = $attributes;

        return $this;
    }

    /**
     * Return the list of default options.
     *
     * @return array
     */
    public function getDefaultOptions(): array
    {
        return $this->defaultOptions;
    }

    /**
     * Return allowed options for the form.
     *
     * @return array
     */
    public function getAllowedOptions(): array
    {
        return $this->allowedOptions;
    }

    /**
     * Set the form locale.
     *
     * @param string $locale
     *
     * @return FormInterface
     */
    public function setLocale(string $locale): FormInterface
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Return the form locale.
     *
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @inheritdoc
     *
     * @param DataTransformerInterface $transformer
     *
     * @return FieldTypeInterface
     */
    public function setTransformer(DataTransformerInterface $transformer): FieldTypeInterface
    {
        return $this;
    }

    /**
     * @inheritdoc
     *
     * @return FieldTypeInterface
     */
    public function build(): FieldTypeInterface
    {
        return $this;
    }

    /**
     * @inheritdoc
     *
     * @param array|string $value
     *
     * @return FieldTypeInterface
     */
    public function setValue($value): FieldTypeInterface
    {
        return $this;
    }

    /**
     * @inheritdoc
     *
     * @return mixed|null
     */
    public function getValue()
    {
        return null;
    }
}
