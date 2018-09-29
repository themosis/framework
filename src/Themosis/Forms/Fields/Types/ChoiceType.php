<?php

namespace Themosis\Forms\Fields\Types;

use Themosis\Core\Application;
use Themosis\Forms\Contracts\CheckableInterface;
use Themosis\Forms\Contracts\SelectableInterface;
use Themosis\Forms\Fields\ChoiceList\ChoiceList;
use Themosis\Forms\Fields\ChoiceList\ChoiceListInterface;
use Themosis\Forms\Fields\Contracts\CanHandleMetabox;
use Themosis\Forms\Transformers\ChoiceToValueTransformer;

class ChoiceType extends BaseType implements CheckableInterface, SelectableInterface, CanHandleMetabox
{
    /**
     * Field layout.
     *
     * @var string
     */
    protected $layout = 'select';

    /**
     * ChoiceType field view.
     *
     * @var string
     */
    protected $view = 'types.choice';

    /**
     * Field type.
     *
     * @var string
     */
    protected $type = 'choice';

    /**
     * The component name.
     *
     * @var string
     */
    protected $component = 'themosis.fields.choice';

    /**
     * The choice field resource transformer.
     *
     * @var string
     */
    protected $resourceTransformer = 'ChoiceFieldTransformer';

    public function __construct(string $name)
    {
        parent::__construct($name);

        $this->allowedOptions = $this->setAllowedOptions();
        $this->defaultOptions = $this->setDefaultOptions();
    }

    /**
     * Define the field allowed options.
     *
     * @return array
     */
    protected function setAllowedOptions(): array
    {
        return array_merge($this->allowedOptions, [
            'choices',
            'expanded',
            'multiple',
            'layout'
        ]);
    }

    /**
     * Define the field default options values.
     *
     * @return array
     */
    protected function setDefaultOptions(): array
    {
        $default = [
            'expanded' => false,
            'multiple' => false,
            'choices' => null
        ];

        if (function_exists('_x')) {
            $default['l10n'] = [
                'not_found' => _x('Nothing found', 'field', Application::TEXTDOMAIN),
                'placeholder' => _x('Select an option...', 'field', Application::TEXTDOMAIN)
            ];
        }

        return array_merge($this->defaultOptions, $default);
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
        $this->setTransformer(new ChoiceToValueTransformer());

        $options = parent::parseOptions($options);

        if (is_null($options['choices'])) {
            $options['choices'] = [];
        }

        if (is_array($options['choices'])) {
            $options['choices'] = new ChoiceList($options['choices']);
        }

        // Set field layout based on field options.
        $this->setLayout($options['expanded'], $options['multiple']);

        // Set the "multiple" attribute for <select> tag.
        if ('select' === $this->getLayout() && $options['multiple']) {
            // We're using a <select> tag with the multiple option set to true.
            // So we're going to directly inject the multiple attribute.
            $options['attributes'][] = 'multiple';
        }

        return $options;
    }

    /**
     * Set the field layout option.
     *
     * @param bool $expanded
     * @param bool $multiple
     *
     * @return $this
     */
    protected function setLayout($expanded = false, $multiple = false)
    {
        if ($expanded && false === $multiple) {
            $this->layout = 'radio';
        } elseif ($expanded && $multiple) {
            $this->layout = 'checkbox';
        }

        return $this;
    }

    /**
     * Return choice type field options.
     *
     * @param array|null $excludes
     *
     * @return array
     */
    public function getOptions(array $excludes = null): array
    {
        $options = parent::getOptions($excludes);

        /*
         * Let's add the choices in a readable format as
         * well as the layout property.
         */
        $choices = $this->getOption('choices', []);

        if ($choices instanceof ChoiceListInterface) {
            $choices = $choices->format()->get();
        }

        return array_merge($options, [
            'choices' => $choices,
            'layout' => $this->getLayout()
        ]);
    }

    /**
     * Retrieve the field layout.
     *
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @inheritdoc
     *
     * @param callable $callback
     * @param array    $args
     *
     * @return string
     */
    public function checked(callable $callback, array $args): string
    {
        return call_user_func_array($callback, $args);
    }

    /**
     * @inheritdoc
     *
     * @param callable $callback
     * @param array    $args
     *
     * @return string
     */
    public function selected(callable $callback, array $args): string
    {
        return call_user_func_array($callback, $args);
    }

    /**
     * Handle metabox field value registration.
     *
     * @param string|array $value
     * @param int          $post_id
     */
    public function metaboxSave($value, int $post_id)
    {
        $this->setValue($value);

        if (! $this->getOption('multiple', false)) {
            // Store single value.
            $this->saveSingleValue($this->getValue(), $post_id);
        } else {
            // Store multiple values.
            $this->saveMultipleValue($this->getValue(), $post_id);
        }
    }

    /**
     * Initialize metabox field value.
     *
     * @param int $post_id
     */
    public function metaboxGet(int $post_id)
    {
        $value = get_post_meta($post_id, $this->getName(), ! $this->getOption('multiple', false));

        if (! empty($value)) {
            $this->setValue($value);
        }
    }

    /**
     * Save a single value.
     *
     * @param string $value
     * @param int    $post_id
     */
    protected function saveSingleValue($value, int $post_id)
    {
        $previous = get_post_meta($post_id, $this->getName(), true);

        if (is_null($value) || empty($value)) {
            delete_post_meta($post_id, $this->getName());
        } elseif (empty($previous)) {
            add_post_meta($post_id, $this->getName(), $value, true);
        } else {
            update_post_meta($post_id, $this->getName(), $value, $previous);
        }
    }

    /**
     * Save multiple values.
     *
     * @param array $value
     * @param int   $post_id
     */
    protected function saveMultipleValue($value, int $post_id)
    {
        $previous = get_post_meta($post_id, $this->getName(), false);

        if (is_null($value) || empty($value)) {
            delete_post_meta($post_id, $this->getName());
        } elseif (empty($previous) && is_array($value)) {
            array_walk($value, function ($val) use ($post_id) {
                add_post_meta($post_id, $this->getName(), $val, false);
            });
        } else {
            delete_post_meta($post_id, $this->getName());

            array_walk($value, function ($val) use ($post_id, $previous) {
                add_post_meta($post_id, $this->getName(), $val, false);
            });
        }
    }
}
