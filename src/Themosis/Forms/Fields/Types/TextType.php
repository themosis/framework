<?php

namespace Themosis\Forms\Fields\Types;

use Themosis\Forms\Contracts\DataTransformerInterface;
use Themosis\Forms\Fields\Contracts\CanHandleMetabox;

class TextType extends BaseType implements DataTransformerInterface, CanHandleMetabox
{
    /**
     * TextType field view.
     *
     * @var string
     */
    protected $view = 'types.text';

    /**
     * Field type.
     *
     * @var string
     */
    protected $type = 'text';

    /**
     * The component name.
     *
     * @var string
     */
    protected $component = 'themosis.fields.text';

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
     * @param mixed $data
     *
     * @return string
     */
    public function transform($data)
    {
        return is_null($data) ? '' : (string) $data;
    }

    /**¨
     * @inheritdoc
     * @param string $data
     * @return string
     */
    public function reverseTransform($data)
    {
        return $this->transform($data);
    }

    /**
     * Initialize text field post meta value.
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
     * Handle text field post meta registration.
     *
     * @param string $value
     * @param int    $post_id
     *
     * @return bool
     */
    public function metaboxSave($value, int $post_id)
    {
        if (is_null($value) || empty($value)) {
            return delete_post_meta($post_id, $this->getName());
        }

        $previous = get_post_meta($post_id, $this->getName(), true);

        if (empty($previous)) {
            return add_post_meta($post_id, $this->getName(), $value, true);
        }

        return update_post_meta($post_id, $this->getName(), $value, $previous);
    }
}
