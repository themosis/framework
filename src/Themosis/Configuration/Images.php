<?php
namespace Themosis\Configuration;

class Images extends ConfigTemplate {

    /**
     * Depending of the child class, will install the given
     * config properties.
     */
    public static function install()
    {
        // Add registered image sizes.
        static::addImages();

        // Add sizes to the media attachment settings dropdown list.
        add_filter('image_size_names_choose', array('\Themosis\Configuration\Images', 'addImagesToDropDownList'));
    }

    /**
     * Loop through the registered image sizes and add them.
     *
     * @return void
     */
    private static function addImages()
    {
        foreach(static::$datas as $slug => $properties){

            list($width, $height, $crop) = $properties;

            add_image_size($slug, $width, $height, $crop);

        }
    }

    /**
     * Add image sizes to the media size dropdown list.
     *
     * @param array $sizes The existing sizes.
     * @return array
     */
    public static function addImagesToDropDownList(array $sizes)
    {
        $new = array();

        foreach(static::$datas as $slug => $properties){

            // If no 4th option, stop the loop.
            if(4 !== count($properties)) break;

            $show = array_pop($properties);

            if($show){
                $new[$slug] = __(static::label($slug), THEMOSIS_FRAMEWORK_TEXTDOMAIN);
            }

        }

        return array_merge($sizes, $new);
    }

    /**
     * Clean the image slug for display.
     * Remove '-', '_' and set first character to uppercase.
     *
     * @param string $text The text to clean.
     * @return string
     */
    private static function label($text)
    {
        return ucwords(str_replace(array('-', '_'), ' ', $text));
    }
}