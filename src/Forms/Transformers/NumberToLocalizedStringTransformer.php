<?php

namespace Themosis\Forms\Transformers;

use Themosis\Forms\Contracts\DataTransformerInterface;
use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Forms\Transformers\Exceptions\DataTransformerException;

class NumberToLocalizedStringTransformer implements DataTransformerInterface
{
    /**
     * Formatter locale.
     *
     * @var string
     */
    protected $locale;

    /**
     * @var FieldTypeInterface
     */
    protected $field;

    public function __construct(string $locale, FieldTypeInterface $field)
    {
        $this->locale = $locale;
        $this->field = $field;
    }

    /**
     * Convert a numeric value to a localized string.
     *
     * @param int|float $data
     *
     * @throws DataTransformerException
     *
     * @return string
     */
    public function transform($data)
    {
        if (is_null($data) || '' === $data) {
            return '';
        }

        if (! is_numeric($data)) {
            throw new DataTransformerException('A numeric value is expected.');
        }

        $formatter = $this->getFormatter();

        $value =  $formatter->format($data);

        if (intl_is_failure($formatter->getErrorCode())) {
            throw new DataTransformerException($formatter->getErrorMessage());
        }

        // Convert fixed spaces to normal ones
        return str_replace("\xc2\xa0", ' ', $value);
    }

    /**
     * Convert a localized string to a numeric value.
     *
     * @param string $data
     *
     * @throws DataTransformerException
     *
     * @return int|float
     */
    public function reverseTransform($data)
    {
        if (is_null($data) || empty($data)) {
            return '';
        }

        $formatter = $this->getFormatter();

        $value = $formatter->parse($data);

        if (intl_is_failure($formatter->getErrorCode())) {
            throw new DataTransformerException($formatter->getErrorMessage());
        }

        return $value;
    }

    /**
     * Retrieve a NumberFormatter instance.
     *
     * @return \NumberFormatter
     */
    protected function getFormatter()
    {
        $formatter = new \NumberFormatter($this->locale, \NumberFormatter::DECIMAL);
        $precision = $this->field->getOption('precision', 0);

        if ($precision) {
            $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, $precision);
        }

        return $formatter;
    }
}
