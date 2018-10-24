<?php

namespace Themosis\Forms\Fields\Types;

use Themosis\Forms\Fields\Contracts\CanHandleMetabox;
use Themosis\Forms\Transformers\IntegerToLocalizedStringTransformer;

class IntegerType extends BaseType implements CanHandleMetabox
{
    /**
     * IntegerType field view.
     *
     * @var string
     */
    protected $view = 'types.number';

    /**
     * Field type.
     *
     * @var string
     */
    protected $type = 'integer';

    /**
     * The component name.
     *
     * @var string
     */
    protected $component = 'themosis.fields.integer';

    /**
     * Parse and setup default options.
     *
     * @param array $options
     *
     * @return array
     */
    protected function parseOptions(array $options): array
    {
        $this->setTransformer(new IntegerToLocalizedStringTransformer($this->getLocale(), $this));

        return parent::parseOptions($options);
    }

    /**
     * Handle integer field post meta registration.
     *
     * @param string $value
     * @param int    $post_id
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
     * Initialize the integer field value.
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
