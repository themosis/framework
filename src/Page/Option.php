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
     *
     * @throws OptionException
     *
     * @return string|array The option value as string or array of values
     */
    public static function get($optionGroup, $name = null)
    {
        $option = get_option($optionGroup);

        if (!empty($option) && !is_null($name)) {
            if (isset($option[$name])) {
                return $option[$name];
            }

            return '';
        } elseif (!empty($option)) {
            return $option;
        }

        return '';
    }
}
