<?php

namespace Themosis\Forms\Fields\Types;

use Themosis\Core\Application;
use Themosis\Forms\Contracts\DataTransformerInterface;
use Themosis\Forms\Fields\Contracts\CanHandleMetabox;
use Themosis\Forms\Fields\Contracts\CanHandlePageSettings;
use Themosis\Forms\Resources\Transformers\CollectionFieldTransformer;

/**
 * Class CollectionType
 *
 * @package Themosis\Forms\Fields\Types
 */
class CollectionType extends BaseType implements DataTransformerInterface, CanHandleMetabox, CanHandlePageSettings
{
    /**
     * Field type.
     *
     * @var string
     */
    protected $type = 'collection';

    /**
     * Field component.
     *
     * @var string
     */
    protected $component = 'themosis.fields.collection';

    /**
     * Resource transformer class.
     *
     * @var string
     */
    protected $resourceTransformer = CollectionFieldTransformer::class;

    public function __construct(string $name)
    {
        parent::__construct($name);

        $this->allowedOptions = $this->setAllowedOptions();
        $this->defaultOptions = $this->setDefaultOptions();
    }

    /**
     * Set field allowed options.
     *
     * @return array
     */
    protected function setAllowedOptions(): array
    {
        return array_merge($this->allowedOptions, [
            'limit'
        ]);
    }

    /**
     * Set field default options values.
     *
     * @return array
     */
    protected function setDefaultOptions(): array
    {
        // A limit of "0" means no-limit.
        $default = [
            'limit' => 0,
            'type' => ['image', 'application']
        ];

        if (function_exists('_x')) {
            $default['l10n'] = [
                'add' => _x('Add Media', 'field', Application::TEXTDOMAIN),
                'button' => _x('Insert', 'field', Application::TEXTDOMAIN),
                'remove' => _x('Remove Selected', 'field', Application::TEXTDOMAIN),
                'title' => _x('Insert Multiple Media', 'field', Application::TEXTDOMAIN)
            ];
        }

        return array_merge($this->defaultOptions, $default);
    }

    /**
     * Parse and setup default options.
     *
     * @param array $options
     *
     * @return array
     */
    protected function parseOptions(array $options): array
    {
        $this->setTransformer($this);

        return parent::parseOptions($options);
    }

    /**
     * @inheritdoc
     *
     * @param array $data
     *
     * @return array
     */
    public function transform($data)
    {
        return empty($data) ? [] : (array) $data;
    }

    /**
     * @inheritdoc
     *
     * @param array $data
     *
     * @return array
     */
    public function reverseTransform($data)
    {
        return $this->transform($data);
    }

    /**
     * Handle collection field post meta registration.
     *
     * @param array $value
     * @param int   $post_id
     */
    public function metaboxSave($value, int $post_id)
    {
        $this->setValue($value);

        $previous = get_post_meta($post_id, $this->getName(), false);

        if (is_null($value) || empty($value)) {
            delete_post_meta($post_id, $this->getName());
        } elseif (empty($previous) && is_array($value)) {
            array_walk($value, function ($val) use ($post_id) {
                add_post_meta($post_id, $this->getName(), $val, false);
            });
        } else {
            delete_post_meta($post_id, $this->getName());

            array_walk($value, function ($val) use ($post_id) {
                add_post_meta($post_id, $this->getName(), $val, false);
            });
        }
    }

    /**
     * Initialize collection field post meta value.
     *
     * @param int $post_id
     */
    public function metaboxGet(int $post_id)
    {
        $value = get_post_meta($post_id, $this->getName(), false);

        if (! empty($value)) {
            $this->setValue($value);
        }
    }

    /**
     * Save the field setting value.
     *
     * @param mixed  $value
     * @param string $name
     */
    public function settingSave($value, string $name)
    {
        //
    }

    /**
     * Return the field setting value.
     *
     * @return mixed|void
     */
    public function settingGet()
    {
        //
    }
}
