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

    /**
     * Parse field options.
     *
     * @param array $options
     *
     * @return array
     */
    protected function parseOptions(array $options): array
    {
        $this->setTransformer(new StringToBooleanTransformer());

        $options = parent::parseOptions($options);

        // Set some default CSS classes if chosen theme is "bootstrap".
        if ('bootstrap' === $options['theme']) {
            $options['attributes']['class'] = isset($options['attributes']['class']) ?
                ' form-check-input' : 'form-check-input';
            $options['label_attr']['class'] = isset($options['label_attr']['class']) ?
                ' form-check-label' : 'form-check-label';
        }

        return $options;
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
