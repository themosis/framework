<?php

namespace Themosis\Forms\Resources\Transformers;

use Themosis\Forms\Contracts\FieldTypeInterface;

class CollectionFieldTransformer extends FieldTransformer
{
    /**
     * Transform field options.
     *
     * @param FieldTypeInterface $field
     *
     * @return array
     */
    protected function getOptions(FieldTypeInterface $field)
    {
        $options = parent::getOptions($field);

        $options['items'] = array_map(function ($id) {
            return [
                'attributes' => wp_prepare_attachment_for_js($id),
                'id' => $id
            ];
        }, (array) $field->getValue());

        return $options;
    }
}
