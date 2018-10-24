<?php

namespace Themosis\Forms\Fields\Types;

class EmailType extends TextType
{
    /**
     * EmailType field view.
     *
     * @var string
     */
    protected $view = 'types.email';

    /**
     * Field type.
     *
     * @var string
     */
    protected $type = 'email';

    /**
     * The component name.
     *
     * @var string
     */
    protected $component = 'themosis.fields.email';
}
