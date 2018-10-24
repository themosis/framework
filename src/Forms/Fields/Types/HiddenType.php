<?php

namespace Themosis\Forms\Fields\Types;

class HiddenType extends TextType
{
    /**
     * HiddenType field view.
     *
     * @var string
     */
    protected $view = 'types.hidden';

    /**
     * Field type.
     *
     * @var string
     */
    protected $type = 'hidden';

    /**
     * The component name.
     *
     * @var string
     */
    protected $component = 'themosis.fields.hidden';

    /**
     * Handle hidden field post meta registration.
     * We do not allow registration as this is a read-only field.
     *
     * @param string $value
     * @param int    $post_id
     *
     * @return null|void
     */
    public function metaboxSave($value, int $post_id)
    {
        return null;
    }
}
