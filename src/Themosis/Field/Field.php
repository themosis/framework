<?php
namespace Themosis\Field;

defined('DS') or die('No direct script access.');

/**
 * Helper class in order to handle properly
 * the creation of custom fields.
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
	 * @param array $properties The field properties.
	 * @param array $extras The field extra parameters.
	 * @return array An array of all properties.
	 */
	private static function parse(array $properties, array $extras)
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
     * @param string $name The text field name.
     * @param array $extras The text field extras parameters.
     * @throws FieldException
     * @return array The text field properties.
     */
	public static function text($name, array $extras = array())
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
     * @param string $name The textarea field name.
     * @param array $extras The textarea field extras parameters.
     * @throws FieldException
     * @return array The textarea field properties.
     */
	public static function textarea($name, array $extras = array())
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
     * Define a single checkbox field.
     *
     * @param string $name The checkbox field name.
     * @param array $extras The checkbox field extras parameters.
     * @throws FieldException
     * @return array The checkbox properties.
     */
	public static function checkbox($name, array $extras = array())
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
     * Define multiple checkboxes
     *
     * @param string $name The checkboxes field name.
     * @param array $options The checkboxes options.
     * @param array $extras The checkboxes field extra parameters.
     * @throws FieldException
     * @return array The checkboxes properties.
     */
	public static function checkboxes($name, array $options, array $extras = array())
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
     * @param string $name The radio field name.
     * @param array $options The radio options.
     * @param array $extras The radio field extra parameters.
     * @throws FieldException
     * @return array The radio field properties.
     */
	public static function radio($name, array $options, array $extras = array())
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
     * Define a select input. Possibility to define <optgroup>
     * tag and if the field allows a multiple selection.
     * In order to define <optgroup>, pass an associative array with as
     * a 'key', the <optgroup> Label and as a 'value' an array of options.
     *
     * @param string $name The select field name.
     * @param array $options The select field options.
     * @param bool $multiple False, True to select multiple options.
     * @param array $extras The select field extra parameters.
     * @throws FieldException
     * @return array The select field properties.
     */
	public static function select($name, array $options, $multiple = false, array $extras = array())
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
     * @param string $name The infinite field name.
     * @param array $fields An array of custom fields to repeat.
     * @param array $extras The infinite field extra parameters.
     * @throws FieldException
     * @return array The infinite field properties.
     */
	public static function infinite($name, array $fields, array $extras = array())
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
     * @param string $name The media field name.
     * @param array $extras The media field extra parameters.
     * @throws FieldException
     * @return array The media field properties.
     */
	public static function media($name, array $extras = array())
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
     * @param string $name The editor field name.
     * @param array $settings The editor field settings. Check Codex: http://codex.wordpress.org/Function_Reference/wp_editor
     * @param array $extras The editor field extra parameters.
     * @throws FieldException
     * @return array The editor field properties
     */
	public static function editor($name, array $settings = array(), array $extras = array())
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
     * @param string $name The section field name.
     * @param array $extras The section field extra parameters.
     * @throws FieldException
     * @return array The section field properties.
     */
	public static function section($name, array $extras = array())
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