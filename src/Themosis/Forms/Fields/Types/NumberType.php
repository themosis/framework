<?php

namespace Themosis\Forms\Fields\Types;

use Themosis\Forms\Fields\Contracts\CanHandleMetabox;
use Themosis\Forms\Transformers\NumberToLocalizedStringTransformer;

class NumberType extends BaseType implements CanHandleMetabox
{
    /**
     * NumberType field view.
     *
     * @var string
     */
    protected $view = 'types.number';

    /**
     * Field type.
     *
     * @var string
     */
    protected $type = 'number';

    /**
     * The component name.
     *
     * @var string
     */
    protected $component = 'themosis.fields.number';

    /**
     * Parse and setup default options.
     *
     * @param array $options
     *
     * @return array
     */
    protected function parseOptions(array $options): array
    {
        $this->setTransformer(new NumberToLocalizedStringTransformer($this->getLocale()));

        return parent::parseOptions($options);
    }

    /**
     * Handle metabox post meta registration.
     *
     * @param mixed $value
     * @param int $post_id
     */
    public function metaboxSave($value, int $post_id)
    {
        $this->setValue($value);

        $previous = get_post_meta($post_id, $this->getName(), true);

        if (is_null($this->getValue()) || empty($this->getValue())) {
            delete_post_meta($post_id, $this->getName());
        } elseif (empty($previous)) {
            add_post_meta($post_id, $this->getName(), $this->getValue(), true);
        } else {
            update_post_meta($post_id, $this->getName(), $this->getValue(), $previous);
        }
    }

    /**
     * Initialize metabox post meta value.
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
}
