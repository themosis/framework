<?php

namespace Themosis\Schema\Contracts;

interface ResourceInterface
{
    /**
     * Set resource data item.
     *
     * @param object $item
     *
     * @return ResourceInterface
     */
    public function using($item): ResourceInterface;

    /**
     * Set resource transformer.
     *
     * @param TransformerInterface $transformer
     *
     * @return ResourceInterface
     */
    public function transformWith(TransformerInterface $transformer): ResourceInterface;

    /**
     * Return the transformed resource data.
     *
     * @return array
     */
    public function get(): array;
}
