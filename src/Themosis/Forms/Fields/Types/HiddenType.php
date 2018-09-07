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
}
