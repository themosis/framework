<?php

namespace Themosis\Forms\Fields\ChoiceList;

class ChoiceList implements ChoiceListInterface
{
    /**
     * Raw choices.
     *
     * @var array
     */
    protected $choices;

    /**
     * Formatted choices.
     *
     * @var array
     */
    protected $results = [];

    public function __construct(array $choices)
    {
        $this->choices = $choices;
    }

    /**
     * Format the choices for use on output.
     *
     * @return ChoiceListInterface
     */
    public function format(): ChoiceListInterface
    {
        if (empty($this->choices)) {
            return $this;
        }

        $this->results = $this->parse($this->choices);

        return $this;
    }

    /**
     * Parse the choices and format them.
     *
     * @param array $choices
     *
     * @return array
     */
    protected function parse(array $choices)
    {
        $items = [];

        foreach ($choices as $key => $value) {
            if (is_array($value)) {
                $items[$key] = $this->parse($value);
            } else {
                if (is_int($key)) {
                    $label = ucfirst(str_replace(['-', '_'], ' ', $value));
                } else {
                    $label = $key;
                }

                $items[$label] = $value;
            }
        }

        return $items;
    }

    /**
     * Return formatted choices.
     *
     * @return array
     */
    public function get(): array
    {
        return $this->results;
    }
}
