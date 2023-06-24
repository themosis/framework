<?php

namespace Themosis\Forms\Resources\Transformers;

use League\Fractal\TransformerAbstract;
use Themosis\Forms\Contracts\FieldTypeInterface;

class FieldTransformer extends TransformerAbstract
{
    protected array $excludedOptions = [
        'attributes',
        'data',
        'data_type',
        'errors',
        'flush',
        'label',
        'label_attr',
        'mapped',
        'messages',
        'placeholder',
        'rules',
        'show_in_rest',
        'theme',
    ];

    /**
     * Transform single field.
     *
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
                'attributes' => $field->getOption('label_attr', []),
            ],
            'theme' => $field->getTheme(),
            'type' => $field->getType(),
            'validation' => [
                'errors' => $field->getOption('errors', true),
                'messages' => $field->error(),
                'placeholder' => $field->getOption('placeholder'),
                'rules' => $field->getOption('rules', ''),
            ],
            'value' => $field->getRawValue(),
        ];
    }

    /**
     * Return field options.
     *
     *
     * @return array
     */
    protected function getOptions(FieldTypeInterface $field)
    {
        $options = $field->getOptions($this->excludedOptions);

        return $options;
    }
}
