<?php
namespace Themosis\Html;

use Themosis\Configuration\Application;

defined('DS') or die('No direct script access.');

class Html
{
	/**
	 * Build a list of HTML attributes from an array.
	 *
	 * @param  array
	 * @return string
	 */
	public static function attributes($attributes)
	{
		$html = array();

		foreach ((array) $attributes as $key => $value)
		{
			// For numeric keys, we will assume that the key and the value are the
			// same, as this will conver HTML attributes such as "required" that
			// may be specified as required="required", etc.
			if (is_numeric($key)) $key = $value;

			if (!is_null($value))
			{
				$html[] = $key.'="'.static::entities($value).'"';
			}
		}

		return (count($html) > 0) ? ' '.implode(' ', $html) : '';
	}

	/**
	 * Convert HTML characters to entities.
	 * The encoding specified in the application configuration file will be used.
	 *
	 * @param  string
	 * @return string
	 */
	public static function entities($value)
	{
		return htmlentities($value, ENT_QUOTES, Application::get('encoding'), false);
	}
}