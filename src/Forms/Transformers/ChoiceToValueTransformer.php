<?php

namespace Themosis\Forms\Transformers;

use Themosis\Forms\Contracts\DataTransformerInterface;

class ChoiceToValueTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     *
     * @param  mixed  $data
     * @return mixed
     */
    public function transform($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'parseNumeric'], $data);
        }

        return $this->parseNumeric($data);
    }

    /**
     * Parse if a value is numeric and cast it
     * to its correct type.
     *
     * @param  mixed  $value
     * @return float|int|mixed
     */
    protected function parseNumeric($value)
    {
        if (is_numeric($value)) {
            if (false !== strrpos($value, '.')) {
                return (float) $value;
            }

            return (int) $value;
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     *
     * @param  mixed  $data
     * @return mixed
     */
    public function reverseTransform($data)
    {
        return $data;
    }
}
