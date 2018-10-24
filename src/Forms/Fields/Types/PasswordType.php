<?php

namespace Themosis\Forms\Fields\Types;

class PasswordType extends TextType
{
    /**
     * PasswordType field view.
     *
     * @var string
     */
    protected $view = 'types.password';

    /**
     * Field type.
     *
     * @var string
     */
    protected $type = 'password';

    /**
     * The component name.
     *
     * @var string
     */
    protected $component = 'themosis.fields.password';
}
