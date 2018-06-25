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

    /**
     * @inheritdoc
     *
     * @param string $value
     *
     * @return FieldTypeInterface
     */
    public function setValue($value): FieldTypeInterface
    {
        parent::setValue($value);

        if ($this->getValue()) {
            // The value is only set on the field when it fails
            // or when the option "flush" is set to "false".
            // If true, let's automatically add the "checked" attribute.
            $this->options['attributes'][] = 'checked';
        }

        return $this;
    }
}
