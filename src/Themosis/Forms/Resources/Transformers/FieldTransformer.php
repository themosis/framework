<?php

namespace Themosis\Forms\Resources\Transformers;

use League\Fractal\TransformerAbstract;
use Themosis\Forms\Contracts\FieldTypeInterface;

class FieldTransformer extends TransformerAbstract
{
    /**
     * @var array
     */
    protected $excludedOptions = [
        'attributes',
        'data',
        'data_type',
        'errors',
        'label',
        'label_attr',
        'messages',
        'placeholder',
        'rules',
        'theme'
    ];

    /**
     * Transform single field.
     *
     * @param FieldTypeInterface $field
     *
     * @return array
     */
    public function transform(FieldTypeInterface $field)
    {
        return [
            'attributes' => $field->getAttributes(),
            'basename' => $field->getBaseName(),
            'component' => $field->getComponent(),
            'data_type' => $field->getOption('data_type', ''),
            'default' => $field->getOption('data', ''),
            'name' => $field->getName(),
            'options' => $this->getOptions($field),
            'label' => [
                'inner' => $field->getOption('label'),
                'attributes' => $field->getOption('label_attr', [])
            ],
            'theme' => $field->getTheme(),
            'type' => $field->getType(),
            'validation' => [
                'errors' => $field->getOption('errors', true),
                'messages' => $field->errors()->toArray(),
                'placeholder' => $field->getOption('placeholder'),
                'rules' => $field->getOption('rules', '')
            ],
            'value' => $field->getValue(''),
        ];
    }

    /**
     * Return field options.
     *
     * @param FieldTypeInterface $field
     *
     * @return array
     */
    protected function getOptions(FieldTypeInterface $field)
    {
        $options = $field->getOptions($this->excludedOptions);

        if ('choice' === $field->getType() && ! empty($options['choices'])) {
            $options['choices'] = $this->parseChoices($options['choices']);
        }

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
