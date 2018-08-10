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
            'data_type' => $field->getOption('data_type'),
            'default' => $field->getOption('data'),
            'name' => $field->getName(),
            'options' => [
                'group' => $field->getOption('group'),
                'info' => $field->getOption('info')
            ],
            'label' => [
                'inner' => $field->getOption('label'),
                'attributes' => $field->getOption('label_attr')
            ],
            'validation' => [
                'errors' => $field->getOption('errors'),
                'messages' => $field->errors()->toArray(),
                'placeholder' => $field->getOption('placeholder'),
                'rules' => $field->getOption('rules')
            ],
            'value' => $field->getValue(),
        ];
    }
}
