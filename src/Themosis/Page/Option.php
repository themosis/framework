<?php
namespace Themosis\Page;

class Option
{
    /**
     * Utility method to retrieve an option saved using the Page class.
     * Give the 'option_group' name and the option 'name' defined by the developer.
     *
     * @param string $optionGroup The section name.
     * @param $name $name The option name.
     * @throws OptionException
     * @return mixed The option value.
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