<?php
namespace Themosis\Field;

defined('DS') or die('No direct script access.');

/**
 * Helper class in order to handle properly
 * the creation of custom metaboxes.
*/

class Field
{

	/**
	 * Allowed field keys
	*/
	private static $allowedKeys = array('title', 'info', 'section', 'default', 'class');

	/**
	 * Check the extras properties and merge them.
	 * If extras are allowed, return them with the field
	 * properties.
	 *
	 * @param array
	 * @param array
	 * @return array
	*/
	private static function parse($properties, $extras)
	{
		$output = array();

		if (is_array($extras) && !empty($extras)) {
			foreach ($extras as $key => $value) {
				if (in_array($key, static::$allowedKeys)) {
					$output[$key] = $value;
				}
			}
		}

		return array_merge($properties, $output);
	}

    /**
     * Define a text input field.
     * Need the name attribute used by the future input.
     *
     * @param string
     * @param array $extras
     * @throws FieldException
     * @internal param $array (optional)
     * @return array
     */
	public static function text($name, $extras = array())
	{
		if (!is_string($name)) {
			throw new FieldException("Invalid name parameter for Field::text method.");
		}

		$properties = array(
			'type'		=> 'text',
			'name'		=> $name
		);

		return static::parse($properties, $extras);
	}

    /**
     * Define a textarea input field.
     *
     * @param string
     * @param array $extras
     * @throws FieldException
     * @internal param $array (optional)
     * @return array
     */
	public static function textarea($name, $extras = array())
	{
		if (!is_string($name)) {
			throw new FieldException("Invalid name parameter for Field::textarea method.");
		}

		$properties = array(
			'type'		=> 'textarea',
			'name'		=> $name
		);

		return static::parse($properties, $extras);
	}

    /**
     * Define a SINGLE checkbox field
     *
     * @param string
     * @param array $extras
     * @throws FieldException
     * @internal param $array (optional)
     * @return array
     */
	public static function checkbox($name, $extras = array())
	{
		if (!is_string($name)) {
			throw new FieldException("Invalid name parameter for Field::checkbox method.");
		}

		$properties = array(
			'type'		=> 'checkbox',
			'name'		=> $name
		);

		return static::parse($properties, $extras);
	}

    /**
     * Define MULTIPLE checkboxes
     *
     * @param $name
     * @param $options
     * @param array $extras
     * @throws FieldException
     * @internal param $string
     * @internal param $array
     * @internal param $array (optional)
     * @return array
     */
	public static function checkboxes($name, $options, $extras = array())
	{
		if (!is_string($name)) {
			throw new FieldException("Invalid name parameter for Field::checkboxes method.");
		} elseif (!is_array($options) || empty($options)) {
			throw new FieldException("You need to pass a non-associative array of options as a second parameter for the Field::checkboxes method.");
		}

		$properties = array(
			'type'		=> 'checkboxes',
			'name'		=> $name,
			'options'	=> $options
		);

		return static::parse($properties, $extras);
	}

    /**
     * Define a radio input
     *
     * @param $name
     * @param $options
     * @param array $extras
     * @throws FieldException
     * @internal param $string
     * @internal param $array
     * @internal param $array (optional)
     * @return array
     */
	public static function radio($name, $options, $extras = array())
	{
		if (!is_string($name)) {
			throw new FieldException("Invalid name parameter for Field::radio method.");
		} elseif (!is_array($options) || empty($options)) {
			throw new FieldException("You need to pass a non-associative array of options as a second parameter for the Field::radio method.");
		}

		$properties = array(
			'type'		=> 'radio',
			'name'		=> $name,
			'options'	=> $options
		);

		return static::parse($properties, $extras);
	}

    /**
     * Define a select input. Possibility to define optgroup
     * tag and if it's a mutliple selection.
     * In order to define optgroup, pass an associative array with as
     * a 'key', the optgroup Label and as a 'value' an array of options.
     *
     * @param $name
     * @param $options
     * @param bool $multiple
     * @param array $extras
     * @throws FieldException
     * @internal param $string
     * @internal param $array
     * @internal param $boolean (optional)
     * @internal param $array (optional)
     * @return array
     */
	public static function select($name, $options, $multiple = false, $extras = array())
	{
		if (!is_string($name)) {
			throw new FieldException("Invalid name parameter for Field::select method.");
		} elseif (!is_array($options) || empty($options)) {
			throw new FieldException("You need to pass an array of options as a second parameter for the Field::select method.");
		} elseif (!is_bool($multiple)) {
			throw new FieldException("You need to pass a boolean as a third parameter for the Field::select method.");
		}

		$properties = array(
			'type'		=> 'select',
			'name'		=> $name,
			'options'	=> $options,
			'multiple'	=> $multiple
		);

		return static::parse($properties, $extras);

	}

    /**
     * Define an Infinite field.
     * Used to build infinite custom fields.
     *
     * @param $name
     * @param $fields
     * @param array $extras
     * @throws FieldException
     * @return array
     */
	public static function infinite($name, $fields, $extras = array())
	{
		if (!is_string($name)) {
			throw new FieldException("Invalid name parameter for Field::infinite method.");
		} elseif (!is_array($fields) || empty($fields)) {
			throw new FieldException("You need to pass an array of fields as a second parameter for the Field::infinite method.");
		}

		$properties = array(
			'type'		=> 'infinite',
			'name'		=> $name,
			'fields'	=> $fields
		);

		return static::parse($properties, $extras);
	}

    /**
     * Define a media field
     * Used to upload images, files, documents, ...
     * For WP > 3.5, use the new WP js object.
     *
     * @param string
     * @param array $extras
     * @throws FieldException
     * @internal param $array (optional)
     * @return array
     */
	public static function media($name, $extras = array())
	{
		if (!is_string($name)) {
			throw new FieldException("Invalid name parameter for Field::media method.");
		}

		$properties = array(
			'type'		=> 'media',
			'name'		=> $name
		);

		return static::parse($properties, $extras);

	}


    /**
     * Define an editor field.
     * Load the WordPress default editor.
     *
     * @access public
     * @static
     * @param string $name - The field identifier.
     * @param array $settings - The editor settings array. Check Codex: http://codex.wordpress.org/Function_Reference/wp_editor
     * @param array $extras - Extras field details (not related to the WordPress Editor)
     * @throws FieldException
     * @return array
     */
	public static function editor($name, $settings = array(), $extras = array())
	{
        if (!is_string($name)) {
            throw new FieldException("Invalid name parameter for Field::editor method.");
        } else if(!is_array($settings)) {
            throw new FieldException("Invalid settings parameter for Field::editor method. Array expected as a second parameter.");
        }

        /*-----------------------------------------------------------------------*/
        // May only contain lower-case letters.
        /*-----------------------------------------------------------------------*/
        $name = strtolower($name);

        $properties = array(

            'type'          => 'editor',
            'name'          => $name,
            'editor_args'   => $settings

        );

        return static::parse($properties, $extras);
	}

    /**
     * Define a section field. Use for options page.
     *
     * @param string
     * @param array $extras
     * @throws FieldException
     * @internal param $array (optional)
     * @return array
     */
	public static function section($name, $extras = array())
	{
		if (!is_string($name)) {
			throw new FieldException("Invalid name parameter for Field::section method.");
		}

		$properties = array(
			'type'		=> 'section',
			'name'		=> $name
		);

		return static::parse($properties, $extras);
	}
}