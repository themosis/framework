<?php

namespace Themosis\Forms\Transformers;

use Themosis\Forms\Contracts\DataTransformerInterface;
use Themosis\Forms\Transformers\Exceptions\DataTransformerException;

class StringToBooleanTransformer implements DataTransformerInterface
{
    /**
     * Convert a string to a boolean value.
     *
     * @param string $data
     *
     * @throws DataTransformerException
     *
     * @return bool
     */
    public function transform($data)
    {
        if (is_bool($data)) {
            return $data;
        }

        if (is_null($data)) {
            return false;
        }

        if (! is_string($data)) {
            throw new DataTransformerException('A string value is expected.');
        }

        if (empty($data)) {
            return false;
        }

        if (in_array($data, ['off', 'no'], true)) {
            return false;
        }

        return true;
    }

    /**
     * Convert a boolean to a string value.
     *
     * @param bool $data
     *
     * @return string
     */
    public function reverseTransform($data)
    {
        if ($data) {
            return 'on';
        }

        return 'off';
    }
}
