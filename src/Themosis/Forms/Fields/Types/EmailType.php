<?php

namespace Themosis\Forms\Fields\Types;

use Themosis\Forms\Contracts\DataTransformerInterface;
use Themosis\Forms\Contracts\FieldTypeInterface;

class EmailType extends BaseType implements DataTransformerInterface
{
    /**
     * EmailType field view.
     *
     * @var string
     */
    protected $view = 'types.email';

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

    /**
     * @inheritdoc
     *
     * @param mixed $data
     *
     * @return string
     */
    public function reverseTransform($data)
    {
        return $this->transform($data);
    }
}
