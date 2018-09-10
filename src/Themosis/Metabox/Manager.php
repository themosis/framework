<?php

namespace Themosis\Metabox;

use Illuminate\Http\Request;
use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Forms\Fields\Contracts\CanHandleMetabox;

class Manager implements MetaboxManagerInterface
{
    /**
     * Handle metabox initialization.
     * Set the metabox fields value and return the metabox instance.
     *
     * @param MetaboxInterface $metabox
     * @param Request          $request
     *
     * @return MetaboxInterface
     */
    public function getFields(MetaboxInterface $metabox, Request $request): MetaboxInterface
    {
        foreach ($metabox->repository()->all() as $field) {
            if (method_exists($field, 'metaboxGet')) {
                $field->metaboxGet($request->query('post_id'));
            }
        }

        return $metabox;
    }

    /**
     * Handle metabox post meta save.
     *
     * @param MetaboxInterface $metabox
     * @param Request          $request
     *
     * @throws MetaboxException
     *
     * @return bool
     */
    public function saveFields(MetaboxInterface $metabox, Request $request): bool
    {
        $post_id = $request->query('post_id');
        $fields = collect($request->get('fields'));

        foreach ($fields as $data) {
            /** @var FieldTypeInterface|CanHandleMetabox $field */
            $field = $metabox->repository()->getField($data['basename'], $data['options']['group']);

            if (method_exists($field, 'metaboxSave')) {
                $field->metaboxSave($data['value'], $post_id);
            } else {
                throw new MetaboxException('Unable to save ['.$field->getName().']. The [metabox] method is missing.');
            }
        }

        return true;
    }
}
