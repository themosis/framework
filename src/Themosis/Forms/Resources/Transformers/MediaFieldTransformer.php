<?php

namespace Themosis\Forms\Resources\Transformers;

use Themosis\Forms\Contracts\FieldTypeInterface;

class MediaFieldTransformer extends FieldTransformer
{
    /**
     * Return media field options.
     *
     * @param FieldTypeInterface $field
     *
     * @return array
     */
    protected function getOptions(FieldTypeInterface $field)
    {
        $options = parent::getOptions($field);

        $options['media'] = [
            'id' => $field->getValue(),
            'name' => get_the_title($field->getValue()),
            'thumbnail' => wp_get_attachment_image_src($field->getValue()),
            'filesize' => '250kb'
        ];

        return $options;
    }
}
