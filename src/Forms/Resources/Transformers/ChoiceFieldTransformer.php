<?php

namespace Themosis\Forms\Resources\Transformers;

use Themosis\Forms\Contracts\FieldTypeInterface;

class ChoiceFieldTransformer extends FieldTransformer
{
    /**
     * Return choice field options.
     *
     * @param FieldTypeInterface $field
     *
     * @return array
     */
    protected function getOptions(FieldTypeInterface $field)
    {
        $options = parent::getOptions($field);

        $options['choices'] = $this->parseChoices($options['choices']);

        return $options;
    }

    /**
     * Parse field choices.
     *
     * @param array $choices
     *
     * @return array
     */
    protected function parseChoices(array $choices)
    {
        $items = [];

        foreach ($choices as $key => $value) {
            if (is_array($value)) {
                // Handle options groups data.
                $items[] = ['key' => $key, 'value' => '', 'type' => 'group'];
                $items = array_merge($items, $this->parseChoices($value));
            } else {
                $items[] = ['key' => $key, 'value' => $value, 'type' => 'option'];
            }
        }

        return $items;
    }
}
