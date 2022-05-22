<?php

namespace Themosis\Html;

class HtmlBuilder
{
    protected string $charset = 'UTF-8';

    public function __construct()
    {
        $this->setCharset();
    }

    /**
     * Set the encoding charset.
     * Defaults to UTF8.
     */
    public function setCharset(?string $charset = null): self
    {
        if (! is_null($charset)) {
            $this->charset = $charset;
        } elseif (defined('THEMOSIS_CHARSET')) {
            $this->charset = THEMOSIS_CHARSET;
        } elseif (function_exists('get_bloginfo')) {
            $this->charset = get_bloginfo('charset');
        }

        return $this;
    }

    /**
     * Build a list of HTML attributes from an array.
     */
    public function attributes(array $attributes): string
    {
        $html = [];

        foreach ((array) $attributes as $key => $value) {
            $elem = $this->attributeElement($key, $value);

            if (! is_null($elem)) {
                $html[] = $elem;
            }
        }

        return (count($html) > 0) ? implode(' ', $html) : '';
    }

    /**
     * Build the attribute.
     */
    protected function attributeElement(string $key, string $value): string
    {
        /**
         * For numeric keys we will assume that the value is a boolean attribute
         * where the presence of the attribute represents a true value and the
         * absence represents a false value.
         * This will convert HTML attributes such as "required" to a correct
         * form instead of using incorrect numeric.
         */
        if (is_numeric($key)) {
            return $value;
        }

        /**
         * Treat boolean attributes as HTML properties.
         */
        if ($key !== 'value') {
            return $value ? $key : '';
        }


        return $key . '="' . $this->special($value) . '"';
    }

    /**
     * Convert HTML characters to entities.
     */
    public function entities(string $value): string
    {
        return htmlentities($value, ENT_QUOTES, $this->charset, false);
    }

    /**
     * Convert special characters to HTML entities.
     */
    public function special(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, $this->charset, false);
    }
}
