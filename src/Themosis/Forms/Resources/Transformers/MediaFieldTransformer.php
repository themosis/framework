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

        $attachedFile = get_attached_file($field->getValue());

        $options['media'] = [
            'name' => wp_basename($attachedFile),
            'thumbnail' => wp_get_attachment_image_src($field->getValue(), 'thumbnail', true)[0],
            'filesize' => round(filesize($attachedFile) / 1024).' KB'
        ];

        return $options;
    }
}
