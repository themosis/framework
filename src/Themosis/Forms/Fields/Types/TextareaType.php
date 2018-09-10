<?php

namespace Themosis\Forms\Fields\Types;

use Themosis\Forms\Contracts\DataTransformerInterface;
use Themosis\Forms\Fields\Contracts\CanHandleMetabox;

class TextareaType extends BaseType implements DataTransformerInterface, CanHandleMetabox
{
    /**
     * TextareaType field view.
     *
     * @var string
     */
    protected $view = 'types.textarea';

    /**
     * Field type.
     *
     * @var string
     */
    protected $type = 'textarea';

    /**
     * The component name.
     *
     * @var string
     */
    protected $component = 'themosis.fields.textarea';

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
     * @param string $data
     *
     * @return string
     */
    public function transform($data)
    {
        return is_null($data) ? '' : (string) $data;
    }

    /**
     * @inheritdoc
     *
     * @param string $data
     *
     * @return string
     */
    public function reverseTransform($data)
    {
        return $this->transform($data);
    }

    /**
     * Initialize textarea field post meta value.
     *
     * @param int $post_id
     */
    public function metaboxGet(int $post_id)
    {
        $value = get_post_meta($post_id, $this->getName(), true);

        if (! empty($value)) {
            $this->setValue($value);
        }
    }

    /**
     * Handle textarea field post meta registration.
     *
     * @param string $value
     * @param int    $post_id
     */
    public function metaboxSave($value, int $post_id)
    {
        $previous = get_post_meta($post_id, $this->getName(), true);

        if (is_null($value) || empty($value)) {
            delete_post_meta($post_id, $this->getName());
        } elseif (empty($previous)) {
            add_post_meta($post_id, $this->getName(), $value, true);
        } else {
            update_post_meta($post_id, $this->getName(), $value, $previous);
        }

        $this->setValue($value);
    }
}
