<?php

namespace Themosis\Forms\Contracts;

interface DataTransformerInterface
{
    /**
     * Return normalized data.
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function transform($data);

    /**
     * Return raw data.
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function reverseTransform($data);
}
