<?php

namespace Themosis\Forms\Fields\Types;

use Themosis\Forms\Transformers\IntegerToLocalizedStringTransformer;

class IntegerType extends BaseType
{
    /**
     * IntegerType field view.
     *
     * @var string
     */
    protected $view = 'types.number';

    /**
     * Field type.
     *
     * @var string
     */
    protected $type = 'integer';

    /**
     * The component name.
     *
     * @var string
     */
    protected $component = 'themosis.fields.integer';

    /**
     * Parse and setup default options.
     *
     * @param array $options
     *
     * @return array
     */
    protected function parseOptions(array $options): array
    {
        $this->setTransformer(new IntegerToLocalizedStringTransformer($this->getLocale()));

        return parent::parseOptions($options);
    }
}
