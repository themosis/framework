<?php

namespace Themosis\Forms\Fields\Types;

use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Forms\Transformers\StringToBooleanTransformer;

class CheckboxType extends BaseType
{
    /**
     * CheckboxType field view.
     *
     * @var string
     */
    protected $view = 'types.checkbox';

    public function build(): FieldTypeInterface
    {
        $this->setTransformer(new StringToBooleanTransformer());

        return $this;
    }
}
