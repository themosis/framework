<?php
namespace Themosis\Page;

defined('DS') or die('No direct script access.');

class Option
{
	/**
	* Utility method to retrieve an option
	* saved using the Page class.
	* Give the 'option_group' name and the
	* option 'name' defined by the developper.
	*
	* @param string
	* @param string
	* @return mixed
	*/
	public static function get($optionGroup, $name)
	{
		$option = get_option($optionGroup);

		if (!empty($option)) {
			$option = $option[$name];

			if (isset($option)) {
				return $option;
			} else {
				throw new OptionException("Invalid option name or value not found.");
			}
		}

		return false;

	}
}