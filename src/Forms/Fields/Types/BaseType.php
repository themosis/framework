<?php

namespace Themosis\Forms\Fields\Types;

use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Contracts\View\Factory as ViewFactoryInterface;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\ResourceInterface;
use League\Fractal\Serializer\ArraySerializer;
use Themosis\Forms\Contracts\DataTransformerInterface;
use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Forms\Contracts\FormInterface;
use Themosis\Forms\FormHelper;
use Themosis\Forms\NullMessageBag;
use Themosis\Forms\Resources\Factory;
use Themosis\Html\HtmlBuilder;

abstract class BaseType extends HtmlBuilder implements \ArrayAccess, \Countable, FieldTypeInterface
{
    use FormHelper;

    /**
     * List of options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * List of allowed options.
     *
     * @var array
     */
    protected $allowedOptions = [
        'attributes',
        'data',
        'data_type',
        'errors',
        'flush',
        'group',
        'info',
        'l10n',
        'label',
        'label_attr',
        'mapped',
        'messages',
        'placeholder',
        'rules',
        'show_in_rest',
        'theme'
    ];

    /**
     * List of default options per field.
     *
     * @var array
     */
    protected $defaultOptions = [
        'attributes' => [],
        'flush' => false,
        'group' => 'default',
        'info' => '',
        'l10n' => [],
        'label' => '',
        'label_attr' => [],
        'mapped' => true,
        'messages' => [],
        'rules' => '',
        'show_in_rest' => false
    ];

    /**
     * Field name prefix.
     * Applied automatically to avoid conflicts with core query variables.
     *
     * @var string
     */
    protected $prefix = 'th_';

    /**
     * The field basename.
     * Name property without the prefix as defined by the user.
     *
     * @var string
     */
    protected $baseName;

    /**
     * Field validation rules.
     *
     * @var string
     */
    protected $rules = '';

    /**
     * A list of custom error messages
     * by field rules.
     *
     * @var array
     */
    protected $messages = [];

    /**
     * A custom :attribute
     * placeholder value.
     *
     * @var string
     */
    protected $placeholder;

    /**
     * The field label display title.
     *
     * @var string
     */
    protected $label;

    /**
     * The form instance handling the field.
     *
     * @var FormInterface
     */
    protected $form;

    /**
     * Indicates if form is rendered.
     *
     * @var bool
     */
    protected $rendered = false;

    /**
     * @var ViewFactoryInterface
     */
    protected $viewFactory;

    /**
     * The field view.
     *
     * @var string
     */
    protected $view;

    /**
     * @var DataTransformerInterface
     */
    protected $transformer;

    /**
     * The "normalized" field value.
     *
     * @var mixed
     */
    protected $value;

    /**
     * Errors message bag.
     *
     * @var MessageBag
     */
    private $errors;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var string
     */
    protected $resourceTransformer = 'FieldTransformer';

    /**
     * Resource factory.
     *
     * @var Factory
     */
    protected $factory;

    /**
     * The field type.
     *
     * @var string
     */
    protected $type = 'input';

    /**
     * The JS component abstract name.
     *
     * @var string
     */
    protected $component;

    /**
     * Field view data.
     *
     * @var array
     */
    private $data = [];

    /**
     * BaseType constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct();
        $this->baseName = $name;
    }

    /**
     * Return the list of default options.
     *
     * @return array
     */
    public function getDefaultOptions(): array
    {
        // Setup default validation rules.
        $this->defaultOptions['rules'] = $this->rules;

        // Setup default messages.
        $this->defaultOptions['messages'] = $this->messages;

        // Setup default placeholder.
        $this->defaultOptions['placeholder'] = $this->placeholder ?? $this->getBaseName();

        // Setup default label.
        $this->defaultOptions['label'] = $this->label ?? ucfirst(str_replace(['-', '_'], ' ', $this->getBaseName()));

        return $this->defaultOptions;
    }

    /**
     * Return allowed options for the field.
     *
     * @return array
     */
    public function getAllowedOptions(): array
    {
        return $this->allowedOptions;
    }

    /**
     * Set field options.
     *
     * @param array $options
     *
     * @return FieldTypeInterface
     */
    public function setOptions(array $options): FieldTypeInterface
    {
        $l10n = $this->handleLocalization($options);

        $options = array_merge(
            $this->getDefaultOptions(),
            $this->options,
            $options
        );

        if (isset($options['l10n']) && ! empty($l10n)) {
            $options['l10n'] = array_merge($options['l10n'], $l10n);
        }

        $this->options = $this->parseOptions($options);

        return $this;
    }

    /**
     * Modify field options by extracting its 'l10n' property
     * and return it.
     *
     * @param array $options
     *
     * @return array
     */
    protected function handleLocalization(array &$options)
    {
        $l10n = [];

        if (isset($options['l10n'])) {
            $l10n = $options['l10n'];
            unset($options['l10n']);
        }

        return $l10n;
    }

    /**
     * Parse and setup some default options if not set.
     *
     * @param array $options
     *
     * @return array
     */
    protected function parseOptions(array $options): array
    {
        // Set a default "id" attribute. This attribute can be used on the field
        // and to its associated label as the "for" attribute value if not set.
        if (! isset($options['attributes']['id'])) {
            $options['attributes']['id'] = $this->getName().'_field';
        }

        // Set the "for" attribute automatically on the label attributes property.
        if (! isset($options['label_attr']['for'])) {
            $options['label_attr']['for'] = $options['attributes']['id'];
        }

        // Set some default CSS classes if chosen theme is "bootstrap".
        if (isset($options['theme']) && 'bootstrap' === $options['theme']) {
            if (isset($options['attributes']['class'])) {
                $options['attributes']['class'] .= ' form-control';
            } else {
                $options['attributes']['class'] = 'form-control';
            }
        }

        // Set default value if defined.
        if (isset($options['data']) && ! is_null($options['data'])) {
            $this->setValue($options['data']);
        }

        return $options;
    }

    /**
     * Return field options.
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
     * Return field options.
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
     * Set the field prefix.
     *
     * @param string $prefix
     *
     * @return FieldTypeInterface
     */
    public function setPrefix(string $prefix): FieldTypeInterface
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Return the field prefix.
     *
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * Return the field theme.
     *
     * @return string
     */
    public function getTheme(): string
    {
        return $this->getOption('theme', '');
    }

    /**
     * Set the field theme.
     *
     * @param string $theme
     *
     * @return FieldTypeInterface
     */
    public function setTheme(string $theme): FieldTypeInterface
    {
        $this->options['theme'] = $theme;

        return $this;
    }

    /**
     * Return the field name property value.
     *
     * @return string
     */
    public function getName(): string
    {
        return trim($this->prefix).$this->getBaseName();
    }

    /**
     * Return the field basename.
     *
     * @return string
     */
    public function getBaseName(): string
    {
        return $this->baseName;
    }

    /**
     * Return the field attributes.
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
     * @param string $name
     *
     * @return mixed
     */
    public function getAttribute(string $name)
    {
        $atts = $this->getAttributes();

        return $atts[$name] ?? '';
    }

    /**
     * Set the field attributes.
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
     * @param bool   $overwrite By default, it appends the value. Set to true, to replace the existing attribute value.
     *
     * @return FieldTypeInterface
     */
    public function addAttribute(string $name, string $value, $overwrite = false): FieldTypeInterface
    {
        if ($overwrite) {
            $this->options['attributes'][$name] = $value;

            return $this;
        }

        if (isset($this->options['attributes'][$name])) {
            $this->options['attributes'][$name] .= ' '.$value;
        } else {
            $this->options['attributes'][$name] = $value;
        }

        return $this;
    }

    /**
     * Setup the form instance handling the field.
     *
     * @param FormInterface $form
     *
     * @return FieldTypeInterface
     */
    public function setForm(FormInterface $form): FieldTypeInterface
    {
        $this->form = $form;

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
        $this->viewFactory = $factory;

        return $this;
    }

    /**
     * Output the entity as HTML.
     *
     * @return string
     */
    public function render(): string
    {
        $view = $this->viewFactory->make($this->getView(), $this->getFieldData());

        // Indicates that the form has been rendered at least once.
        // Then return its content.
        $this->rendered = true;

        return $view->render();
    }

    /**
     * Generate and get field data.
     *
     * @return array
     */
    protected function getFieldData(): array
    {
        return array_merge($this->data, [
            '__field' => $this
        ]);
    }

    /**
     * Pass custom data to the field view.
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
     * Return the view instance used by the entity.
     *
     * @param bool $prefixed
     *
     * @return string
     */
    public function getView(bool $prefixed = true): string
    {
        if ($prefixed && ! is_null($theme = $this->getOption('theme'))) {
            return $this->buildViewPath($theme, $this->view);
        }

        return $this->view;
    }

    /**
     * Indicates if the entity has been rendered or not.
     *
     * @return bool
     */
    public function isRendered(): bool
    {
        return $this->rendered;
    }

    /**
     * Check if field is valid.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return empty($this->error());
    }

    /**
     * Check if field is not valid.
     *
     * @return bool
     */
    public function isNotValid(): bool
    {
        return ! $this->isValid();
    }

    /**
     * Set the field transformer.
     *
     * @param DataTransformerInterface $transformer
     *
     * @return FieldTypeInterface
     */
    public function setTransformer(DataTransformerInterface $transformer): FieldTypeInterface
    {
        $this->transformer = $transformer;

        return $this;
    }

    /**
     * Set the value property of the field.
     *
     * @param array|string $value
     *
     * @return FieldTypeInterface
     */
    public function setValue($value): FieldTypeInterface
    {
        $this->value = $this->transformer->transform($value);

        return $this;
    }

    /**
     * Retrieve the field "normalized" value.
     *
     * @param mixed $default
     *
     * @return mixed
     */
    public function getValue($default = null)
    {
        if (is_null($this->value) && ! is_null($default)) {
            return $default;
        }

        return $this->value;
    }

    /**
     * Retrieve the field "raw" value.
     *
     * @return mixed
     */
    public function getRawValue()
    {
        $value = $this->transformer->reverseTransform($this->value);

        if ($this->getOption('flush', false)) {
            return '';
        }

        return $value;
    }

    /**
     * Set the field error message bag instance.
     *
     * @param MessageBag $messageBag
     *
     * @return $this
     */
    public function setErrorMessageBag($messageBag)
    {
        $this->errors = $messageBag;

        return $this;
    }

    /**
     * Return the field error message bag instance.
     *
     * @return MessageBag
     */
    public function errors(): MessageBag
    {
        if (is_null($this->errors)) {
            return new NullMessageBag();
        }

        return $this->errors;
    }

    /**
     * Retrieve the field error messages.
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
            $name = $this->getName();
        }

        if ($first) {
            return $errors->first($name);
        }

        return $errors->get($name);
    }

    /**
     * Return the field locale.
     *
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * Set the field locale.
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
     * Return the Fractal manager.
     *
     * @return Manager
     */
    public function getManager(): Manager
    {
        return $this->manager;
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
     * Return the transformer factory.
     *
     * @return Factory
     */
    public function getResourceTransformerFactory(): Factory
    {
        return $this->factory;
    }

    /**
     * Set the transformer factory.
     *
     * @param Factory $factory
     *
     * @return FieldTypeInterface
     */
    public function setResourceTransformerFactory(Factory $factory): FieldTypeInterface
    {
        $this->factory = $factory;

        return $this;
    }

    /**
     * Return the field resource transformer class name.
     *
     * @return string
     */
    public function getResourceTransformer(): string
    {
        return $this->resourceTransformer;
    }

    /**
     * Define the Fractal resource used by the field.
     *
     * @return ResourceInterface
     */
    protected function resource(): ResourceInterface
    {
        return new Item($this, $this->getResourceTransformerFactory()->make($this->resourceTransformer));
    }

    /**
     * Define the serialization for the field resource.
     *
     * @return FieldTypeInterface
     */
    protected function serialize(): FieldTypeInterface
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
     * Return a JSON representation of the field.
     *
     * @return string
     */
    public function toJson(): string
    {
        return $this->serialize()->getManager()->createData($this->resource())->toJson();
    }

    /**
     * Return the field type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Return the field component name.
     *
     * @return string
     */
    public function getComponent(): string
    {
        return $this->component;
    }

    /**
     * Whether a offset exists.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset An offset to check for.
     *
     * @return bool true on success or false on failure.
     *
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->options[$offset]);
    }

    /**
     * Offset to retrieve
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset The offset to retrieve.
     *
     * @return mixed Can return all value types.
     *
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return isset($this->options[$offset]) ? $this->options[$offset] : null;
    }

    /**
     * Offset to set
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset The offset to assign the value to.
     * @param mixed $value  The value to set.
     *
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->options[] = $value;
        } else {
            $this->options[$offset] = $value;
        }
    }

    /**
     * Offset to unset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset The offset to unset.
     *
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        unset($this->options[$offset]);
    }

    /**
     * Count elements of an object
     *
     * @link http://php.net/manual/en/countable.count.php
     *
     * @return int The custom count as an integer.
     *
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->options);
    }
}
