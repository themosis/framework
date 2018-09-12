<?php

namespace Themosis\Forms\Resources\Transformers;

use League\Fractal\TransformerAbstract;
use Themosis\Forms\Contracts\FieldTypeInterface;

class FieldTransformer extends TransformerAbstract
{
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
            'options' => $field->getOptions([
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
            ]),
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
     * Attach properties to transformed output.
     *
     * @param FieldTypeInterface $field
     * @param \Closure           $callback
     *
     * @return array
     */
    protected function with(FieldTypeInterface $field, \Closure $callback): array
    {
        return $callback($field);
    }
}
