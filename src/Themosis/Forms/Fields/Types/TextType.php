<?php

namespace Themosis\Forms\Fields\Types;

use Themosis\Forms\Contracts\DataTransformerInterface;
use Themosis\Forms\Contracts\FieldTypeInterface;

class TextType extends BaseType implements DataTransformerInterface
{
    /**
     * TextType field view.
     *
     * @var string
     */
    protected $view = 'types.text';

    /**
     * Setup the field.
     *
     * @return FieldTypeInterface
     */
    public function build(): FieldTypeInterface
    {
        $this->setTransformer($this);

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @param mixed $data
     *
     * @return string
     */
    public function transform($data)
    {
        return is_null($data) ? '' : (string) $data;
    }

    /**¨
     * @inheritdoc
     * @param string $data
     * @return string
     */
    public function reverseTransform($data)
    {
        return $this->transform($data);
    }
}
