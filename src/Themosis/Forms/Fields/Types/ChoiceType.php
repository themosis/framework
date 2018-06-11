<?php

namespace Themosis\Forms\Fields\Types;

use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Forms\Fields\ChoiceList\ChoiceList;
use Themosis\Forms\Transformers\ChoiceToValueTransformer;

class ChoiceType extends BaseType
{
    public function __construct(string $name)
    {
        parent::__construct($name);

        $this->allowedOptions = $this->setAllowedOptions();
        $this->defaultOptions = $this->setDefaultOptions();
    }

    /**
     * Define the field allowed options.
     *
     * @return array
     */
    protected function setAllowedOptions()
    {
        return array_merge($this->allowedOptions, [
            'choices',
            'expanded',
            'multiple'
        ]);
    }

    /**
     * Define the field default options values.
     *
     * @return array
     */
    protected function setDefaultOptions()
    {
        return array_merge($this->defaultOptions, [
            'expanded' => false,
            'multiple' => false,
            'choices' => null
        ]);
    }

    /**
     * Parse and setup some default options if not set.
     *
     * @param array $options
     *
     * @return array
     */
    protected function parseOptions(array $options)
    {
        $options = parent::parseOptions($options);

        if (is_null($options['choices'])) {
            $options['choices'] = [];
        }

        $options['choices'] = new ChoiceList($options['choices']);

        return $options;
    }

    /**
     * Setup field.
     *
     * @return FieldTypeInterface
     */
    public function build(): FieldTypeInterface
    {
        $this->setTransformer(new ChoiceToValueTransformer());

        return $this;
    }
}
