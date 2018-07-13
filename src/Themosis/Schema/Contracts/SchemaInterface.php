<?php

namespace Themosis\Schema\Contracts;

interface SchemaInterface
{
    /**
     * Set the schema resource.
     *
     * @param ResourceInterface $resource
     *
     * @return SchemaInterface
     */
    public function from(ResourceInterface $resource): SchemaInterface;

    /**
     * Return the schema from the resource as an array.
     *
     * @return array
     */
    public function toArray(): array;

    /**
     * Return the schema from the resource as a JSON string.
     *
     * @return string
     */
    public function toJSON(): string;
}
