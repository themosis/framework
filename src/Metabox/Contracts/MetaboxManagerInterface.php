<?php

namespace Themosis\Metabox\Contracts;

use Illuminate\Http\Request;

interface MetaboxManagerInterface
{
    /**
     * Handle metabox initialization.
     * Initialize metabox fields values.
     */
    public function getFields(MetaboxInterface $metabox, Request $request): MetaboxInterface;

    /**
     * Handle metabox post meta save.
     */
    public function saveFields(MetaboxInterface $metabox, Request $request): MetaboxInterface;
}
