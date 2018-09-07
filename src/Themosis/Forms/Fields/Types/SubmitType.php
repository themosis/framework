<?php

namespace Themosis\Forms\Fields\Types;

class SubmitType extends ButtonType
{
    /**
     * SubmitType field view.
     *
     * @var string
     */
    protected $view = 'types.submit';

    /**
     * Field type.
     *
     * @var string
     */
    protected $type = 'submit';

    /**
     * The component name.
     *
     * @var string
     */
    protected $component = 'themosis.fields.submit';
}
