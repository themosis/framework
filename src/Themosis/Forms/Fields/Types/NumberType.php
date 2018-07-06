<?php

namespace Themosis\Forms\Fields\Types;

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
     * Parse and setup default options.
     *
     * @param array $options
     *
     * @return array
     */
    protected function parseOptions(array $options): array
    {
        $this->setTransformer(new NumberToLocalizedStringTransformer($this->getLocale()));

        return parent::parseOptions($options);
    }
}
