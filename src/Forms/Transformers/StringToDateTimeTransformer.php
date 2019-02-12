<?php

namespace Themosis\Forms\Transformers;

use DateTime;
use Themosis\Forms\Contracts\DataTransformerInterface;

class StringToDateTimeTransformer implements DataTransformerInterface
{
    /**
     * DateTime local with seconds
     */
    private const DATETIME_LOCAL_SECONDS = 'Y-m-d\TH:i:s';

    /**
     * Convert a string to a DateTime value.
     *
     * @param string $data
     *
     * @return string|DateTime
     */
    public function transform($data)
    {
        return is_null($data) ? '' : DateTime::createFromFormat(self::DATETIME_LOCAL_SECONDS, $data);
    }

    /**
     * Convert a DateTime to a string value.
     *
     * @param DateTime $data
     *
     * @return string
     */
    public function reverseTransform($data)
    {
        return is_null($data) ? '' : $data->format(self::DATETIME_LOCAL_SECONDS);
    }
}
