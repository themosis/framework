<?php

namespace Themosis\Forms\Fields\Types;

use Themosis\Forms\Contracts\DataTransformerInterface;
use Themosis\Forms\Contracts\FieldTypeInterface;

class ButtonType extends BaseType implements DataTransformerInterface
{
    /**
     * ButtonType field view.
     *
     * @var string
     */
    protected $view = 'types.button';

    public function build(): FieldTypeInterface
    {
        $this->setTransformer($this);

        return $this;
    }

    /**
     * Get default button options.
     *
     * @return array
     */
    public function getDefaultOptions(): array
    {
        $options = parent::getDefaultOptions();

        // Check the "type" attribute. If it is not set,
        // let's define it by default to "submit".
        if (! isset($options['attributes']['type'])) {
            $options['attributes']['type'] = 'submit';
        }

        return $options;
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
}
