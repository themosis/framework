<?php

namespace Themosis\Metabox\Resources;

interface MetaboxResourceInterface
{
    /**
     * Set the metabox data source element.
     *
     * @param mixed $source
     *
     * @return MetaboxResourceInterface
     */
    public function setSource($source): MetaboxResourceInterface;

    /**
     * Return an array representation of the data source.
     *
     * @return array
     */
    public function toArray(): array;

    /**
     * Return a JSON representation of the data source.
     *
     * @return string
     */
    public function toJson(): string;
}
