<?php

namespace Themosis\Schema;

use Themosis\Schema\Contracts\ResourceInterface;
use Themosis\Schema\Contracts\TransformerInterface;

class Resource implements ResourceInterface
{
    /**
     * Original data.
     *
     * @var mixed
     */
    private $item;

    /**
     * Transformed data.
     *
     * @var array
     */
    private $data = [];

    /**
     * Set resource data item.
     *
     * @param object $item
     *
     * @return ResourceInterface
     */
    public function using($item): ResourceInterface
    {
        $this->item = $item;

        return $this;
    }

    /**
     * Set resource transformer.
     *
     * @param TransformerInterface $transformer
     *
     * @return ResourceInterface
     */
    public function transformWith(TransformerInterface $transformer): ResourceInterface
    {
        $this->data = $transformer->transform($this->item);

        return $this;
    }

    /**
     * Return the transformed resource data.
     *
     * @return array
     */
    public function get(): array
    {
        return $this->data;
    }
}
