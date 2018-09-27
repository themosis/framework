<?php

namespace Themosis\Forms\Fields\Types;

class ColorType extends TextType
{
    /**
     * Color field view.
     *
     * @var string
     */
    protected $view = 'types.text';

    /**
     * Field type.
     *
     * @var string
     */
    protected $type = 'color';

    /**
     * The component name.
     *
     * @var string
     */
    protected $component = 'themosis.fields.color';
}
