<?php

namespace Themosis\Html;

class HtmlBuilder
{
    /**
     * @var string
     */
    protected $charset = 'UTF-8';

    public function __construct()
    {
        $this->setCharset();
    }

    /**
     * Set the encoding charset.
     * Defaults to UTF8.
     *
     * @param string $charset
     *
     * @return \Themosis\Html\HtmlBuilder
     */
    public function setCharset($charset = null)
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
     *
     * @param array $attributes An array of html tag attributes.
     *
     * @return string The html attributes output.
     */
    public function attributes(array $attributes)
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
     *
     * @param string $key
     * @param string $value
     *
     * @return string|null
     */
    protected function attributeElement($key, $value)
    {
        // For numeric keys we will assume that the value is a boolean attribute
        // where the presence of the attribute represents a true value and the
        // absence represents a false value.
        // This will convert HTML attributes such as "required" to a correct
        // form instead of using incorrect numeric.
        if (is_numeric($key)) {
            return $value;
        }
        // Treat boolean attributes as HTML properties
        if (is_bool($value) && $key !== 'value') {
            return $value ? $key : '';
        }
        if (! is_null($value)) {
            return $key.'="'.$this->special($value).'"';
        }

        return null;
    }

    /**
     * Convert HTML characters to entities.
     *
     * @param string $value A string to encode.
     *
     * @return string The encoded character.
     */
    public function entities($value)
    {
        return htmlentities($value, ENT_QUOTES, $this->charset, false);
    }

    /**
     * Convert special characters to HTML entities.
     *
     * @param string $value
     *
     * @return string
     */
    public function special($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, $this->charset, false);
    }
}
