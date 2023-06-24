<?php

namespace Themosis\Forms\Fields\Contracts;

interface CanHandleMetabox
{
    /**
     * Handle metabox post meta save action.
     * Register the field data.
     *
     * @param  mixed  $value
     */
    public function metaboxSave($value, int $post_id);

    /**
     * Initialize field post meta value.
     */
    public function metaboxGet(int $post_id);
}
