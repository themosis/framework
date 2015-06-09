<?php
namespace Themosis\Html;

class HtmlBuilder
{
	/**
	 * Build a list of HTML attributes from an array.
	 *
	 * @param  array $attributes An array of html tag attributes.
	 * @return string The html attributes output.
	 */
	public function attributes(array $attributes)
	{
		$html = [];

		foreach ((array) $attributes as $key => $value)
		{
			// For numeric keys, we will assume that the key and the value are the
			// same, as this will convert HTML attributes such as "required" that
			// may be specified as required="required", etc.
			if (is_numeric($key)) $key = $value;

			if (!is_null($value))
			{
				$html[] = $key.'="'.$this->entities($value).'"';
			}
		}

		return (count($html) > 0) ? ' '.implode(' ', $html) : '';
	}

	/**
	 * Convert HTML characters to entities.
	 * The encoding specified in the application configuration file will be used.
	 *
	 * @param string $value A character to encode.
	 * @return string The encoded character.
	 */
	public function entities($value)
    {
		return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
	}
}