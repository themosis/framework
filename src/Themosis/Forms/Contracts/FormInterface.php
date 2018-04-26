<?php

namespace Themosis\Forms\Contracts;

use Themosis\Forms\Fields\FieldBuilder;

interface FormInterface
{
    public function configure(FieldBuilder $builder);
}
