<?php

namespace Themosis\Forms\Fields\Types;

use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Forms\Transformers\NumberToLocalizedStringTransformer;

class NumberType extends BaseType
{
    /**
     * NumberType field view.
     *
     * @var string
     */
    protected $view = 'types.number';

    /**
     * Setup the field.
     *
     * @return FieldTypeInterface
     */
    public function build(): FieldTypeInterface
    {
        $this->setTransformer(new NumberToLocalizedStringTransformer($this->getLocale()));

        return $this;
    }
}
