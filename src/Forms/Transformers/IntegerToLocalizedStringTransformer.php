<?php

namespace Themosis\Forms\Transformers;

class IntegerToLocalizedStringTransformer extends NumberToLocalizedStringTransformer
{
    /**
     * Convert a localized string to an integer value.
     *
     * @param  string  $data
     * @return int
     *
     * @throws Exceptions\DataTransformerException
     */
    public function reverseTransform($data)
    {
        $value = parent::reverseTransform($data);

        return ! is_null($value) ? (int) $value : null;
    }
}
