<?php

namespace Themosis\Html;

class HtmlBuilder
{
    /**
     * Build a list of HTML attributes from an array.
     *
     * @param array $attributes An array of html tag attributes.
     *
     * @return string The html attributes output.
     */
    public function attributes(array $attributes)
    {
        $html = [];

        foreach ((array) $attributes as $key => $value) {
            // For numeric keys, we will assume that the key and the value are the
            // same, as this will convert HTML attributes such as "required" that
            // may be specified. Those attributes are HTML5 formatted.
            if (is_numeric($key)) {
                $html[] = $this->entities($value);
            } elseif (is_string($key) && (is_numeric($value) || !empty($value))) {
                $html[] = $key.'="'.$this->entities($value).'"';
            }
        }

        return (count($html) > 0) ? ' '.implode(' ', $html) : '';
    }

    /**
     * Convert HTML characters to entities.
     *
     * @param string $value A character to encode.
     *
     * @return string The encoded character.
     */
    public function entities($value)
    {
        $charset = defined('THEMOSIS_CHARSET') ? THEMOSIS_CHARSET : get_bloginfo('charset');

        return htmlentities($value, ENT_QUOTES, $charset, false);
    }
}
