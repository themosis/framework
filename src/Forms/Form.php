<?php

namespace Themosis\Forms;

use DomainException;
use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Contracts\Validation\Factory as ValidationFactoryInterface;
use Illuminate\Contracts\View\Factory as ViewFactoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Factory;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\ResourceInterface;
use League\Fractal\Serializer\ArraySerializer;
use Themosis\Forms\Contracts\DataTransformerInterface;
use Themosis\Forms\Contracts\FieldsRepositoryInterface;
use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Forms\Contracts\FormInterface;
use Themosis\Forms\DataMappers\DataMapperManager;
use Themosis\Forms\Fields\Types\BaseType;
use Themosis\Forms\Resources\Factory as TransformerFactory;
use Themosis\Html\HtmlBuilder;
use Themosis\Support\Contracts\SectionInterface;

/**
 * Class Form
 *
 * @package Themosis\Forms
 */
class Form extends HtmlBuilder implements FormInterface, FieldTypeInterface
{
    use FormHelper;

    /**
     * DTO object.
     *
     * @var mixed
     */
    protected $dataClass;

    /**
     * @var DataMapperManager
     */
    protected $dataMapper;

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
     * @var FieldsRepositoryInterface
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
        'attributes',
        'container_attr',
        'csrf',
        'errors',
        'flush',
        'nonce',
        'nonce_action',
        'referer',
        'tags',
        'theme'
    ];

    /**
     * List of default form options.
     *
     * @var array
     */
    protected $defaultOptions = [
        'attributes' => [],
        'container_attr' => [],
        'csrf' => true,
        'flush' => true,
        'tags' => true,
        'errors' => true,
        'theme' => 'themosis'
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

    /**
     * Fractal manager instance.
     *
     * @var Manager
     */
    protected $manager;

    /**
     * @var TransformerFactory
     */
    protected $factory;

    /**
     * The resource transformer class.
     *
     * @var string
     */
    protected $resourceTransformer = 'FormTransformer';

    /**
     * Form type.
     *
     * @var string
     */
    protected $type = 'form';

    /**
     * The JS component name.
     *
     * @var string
     */
    protected $component;

    /**
     * Form view data.
     *
     * @var array
     */
    private $data = [];

    public function __construct(
        $dataClass,
        FieldsRepositoryInterface $repository,
        ValidationFactoryInterface $validation,
        ViewFactoryInterface $viewer,
        DataMapperManager $dataMapper
    ) {
        parent::__construct();
        $this->dataClass = $dataClass;
        $this->repository = $repository;
        $this->validation = $validation;
        $this->viewer = $viewer;
        $this->dataMapper = $dataMapper;

        /** @var Factory $validation */
        $this->setLocale($validation->getTranslator()->getLocale());
    }

    /**
     * Get the form repository instance.
     *
     * @return FieldsRepositoryInterface
     */
    public function repository(): FieldsRepositoryInterface
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
     * Return the form theme.
     *
     * @return string
     */
    public function getTheme(): string
    {
        return $this->getOption('theme');
    }

    /**
     * Set the form and attached fields theme.
     *
     * @param string $theme
     *
     * @return FieldTypeInterface
     */
    public function setTheme(string $theme): FieldTypeInterface
    {
        $this->options['theme'] = $theme;

        // Update all attached fields with the given theme.
        foreach ($this->repository->all() as $field) {
            /** @var FieldTypeInterface $field */
            $field->setTheme($theme);
        }

        // Update all attached groups with the given theme.
        foreach ($this->repository()->getGroups() as $group) {
            /** @var SectionInterface $group */
            $group->setTheme($theme);
        }

        return $this;
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
        // Set each field value.
        // Update the DTO instance with form data if defined.
        array_walk($fields, function ($field) use ($data) {
            /** @var $field BaseType */
            $field->setErrorMessageBag($this->errors());

            // Set the field value. Each field has its own data transformer so when we
            // call the field getValue() method later on, we're sure to fetch a correct
            // formatted value.
            $field->setValue(Arr::get($data, $field->getName()));

            // DTO
            if (! is_null($this->dataClass) && is_object($this->dataClass) && $field->getOption('mapped')) {
                $this->dataMapper->mapFromFieldToObject($field, $this->dataClass);
            }

            // By default, if the form is not valid, we keep populating fields values.
            // In the case of a valid form, by default, values are flushed except if
            // the "flush" option for the form has been set to true.
            if ($this->validator->fails()) {
                if ($field->error()) {
                    // Add invalid CSS classes.
                    $field->addAttribute('class', 'is-invalid');
                } else {
                    // Add valid CSS classes and validate the field.
                    $field->addAttribute('class', 'is-valid');
                }
            } else {
                // Validation is successful, we can flush fields value at output.
                if ($this->getOption('flush', false)) {
                    $field['flush'] = true;
                }
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
            $rules[$field->getName()] = $field->getOption('rules');
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
            foreach ($field->getOption('messages') as $attr => $message) {
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
            $attributes[$field->getName()] = $field->getOption('placeholder');
        }

        return $attributes;
    }

    /**
     * Check if submitted form is valid.
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
     * Check if submitted form is not valid.
     *
     * @return bool
     */
    public function isNotValid(): bool
    {
        return ! $this->isValid();
    }

    /**
     * Return a list of form errors.
     *
     * @return MessageBag
     */
    public function errors(): MessageBag
    {
        if (is_null($this->validator)) {
            return new NullMessageBag();
        }

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
        return array_merge($this->data, [
            '__form' => $this
        ]);
    }

    /**
     * Pass custom data to the form view.
     *
     * @param array|string $key
     * @param null         $value
     *
     * @return FieldTypeInterface
     */
    public function with($key, $value = null): FieldTypeInterface
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }

        return $this;
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
     * Set the field view factory instance.
     *
     * @param ViewFactoryInterface $factory
     *
     * @return FieldTypeInterface
     */
    public function setViewFactory(ViewFactoryInterface $factory): FieldTypeInterface
    {
        $this->viewer = $factory;

        return $this;
    }

    /**
     * Return the view path instance used by the form.
     *
     * @param bool $prefixed
     *
     * @return string
     */
    public function getView(bool $prefixed = true): string
    {
        if ($prefixed) {
            return $this->buildViewPath($this->getTheme(), $this->view);
        }

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

        // Make sure a default theme is always defined. User cannot defined an
        // empty string for the form theme.
        if (! isset($this->options['theme'])) {
            $this->setTheme('themosis');
        }

        return $options;
    }

    /**
     * Return form options.
     *
     * @param array $excludes
     *
     * @return array
     */
    public function getOptions(array $excludes = null): array
    {
        if (! is_null($excludes)) {
            return array_filter($this->options, function ($key) use ($excludes) {
                return ! in_array($key, $excludes, true);
            }, ARRAY_FILTER_USE_KEY);
        }

        return $this->options;
    }

    /**
     * Return form options.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return string|array|null
     */
    public function getOption(string $key, $default = null)
    {
        return $this->options[$key] ?? $default;
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
        return $this->getOption('attributes');
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
     * Add an attribute to the field.
     *
     * @param string $name
     * @param string $value
     * @param bool   $overwrite
     *
     * @return FieldTypeInterface
     */
    public function addAttribute(string $name, string $value, $overwrite = false): FieldTypeInterface
    {
        if (isset($this->options['attributes'][$name]) && ! $overwrite) {
            $this->options['attributes'][$name] .= ' '.$value;
        } else {
            $this->options['attributes'][$name] = $value;
        }

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
     * @return FieldTypeInterface
     */
    public function setLocale(string $locale): FieldTypeInterface
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
     * @param mixed $default
     *
     * @return mixed|null
     */
    public function getValue($default = null)
    {
        return null;
    }

    /**
     * @inheritdoc
     *
     * @return mixed
     */
    public function getRawValue()
    {
        return null;
    }

    /**
     * Set the Fractal manager.
     *
     * @param Manager $manager
     *
     * @return FieldTypeInterface
     */
    public function setManager(Manager $manager): FieldTypeInterface
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * Return the Fractal manager.
     *
     * @return Manager
     */
    public function getManager(): Manager
    {
        return $this->manager;
    }

    /**
     * Return the transformer factory.
     *
     * @return TransformerFactory
     */
    public function getResourceTransformerFactory(): TransformerFactory
    {
        return $this->factory;
    }

    /**
     * Set the transformer factory.
     *
     * @param TransformerFactory $factory
     *
     * @return FieldTypeInterface
     */
    public function setResourceTransformerFactory(TransformerFactory $factory): FieldTypeInterface
    {
        $this->factory = $factory;

        return $this;
    }

    /**
     * Return form resource transformer class name.
     *
     * @return string
     */
    public function getResourceTransformer(): string
    {
        return $this->resourceTransformer;
    }

    /**
     * Define the Fractal resource used by the form.
     *
     * @return ResourceInterface
     */
    protected function resource(): ResourceInterface
    {
        return new Item($this, $this->getResourceTransformerFactory()->make($this->resourceTransformer));
    }

    /**
     * Define the serialization for the form resource.
     *
     * @return Form
     */
    protected function serialize(): Form
    {
        $this->manager->setSerializer(new ArraySerializer());

        return $this;
    }

    /**
     * Return an associative array representation of the field.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->serialize()->getManager()->createData($this->resource())->toArray();
    }

    /**
     * Return a JSON representation of the form instance.
     *
     * @return string
     */
    public function toJson(): string
    {
        return $this->serialize()->getManager()->createData($this->resource())->toJson();
    }

    /**
     * Return the form type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Return the form component name.
     *
     * @return string
     */
    public function getComponent(): string
    {
        return $this->component;
    }

    /**
     * Return form HTML element open tag.
     *
     * @return string
     */
    public function open(): string
    {
        return $this->getOption('tags')
            ? sprintf(
                '<form %s>',
                $this->attributes($this->getAttributes())
            )
            : '';
    }

    /**
     * Return form HTML element close tag.
     *
     * @return string
     */
    public function close(): string
    {
        return $this->getOption('tags') ? '</form>' : '';
    }
}
