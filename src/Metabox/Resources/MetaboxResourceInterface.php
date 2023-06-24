<?php

namespace Themosis\Metabox\Resources;

interface MetaboxResourceInterface
{
    /**
     * Set the metabox data source element.
     *
     * @param  mixed  $source
     */
    public function setSource($source): MetaboxResourceInterface;

    /**
     * Return an array representation of the data source.
     */
    public function toArray(): array;

    /**
     * Return a JSON representation of the data source.
     */
    public function toJson(): string;
}
