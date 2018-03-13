<?php

namespace Themosis\Config;

use Themosis\Hook\IHook;

class Images
{
    /**
     * The image sizes.
     *
     * @var array
     */
    protected $data = [];

    /**
     * @var IHook
     */
    protected $filter;

    public function __construct(array $data, IHook $filter)
    {
        $this->data = $data;
        $this->filter = $filter;
    }

    /**
     * Add custom image sizes.
     *
     * @return \Themosis\Config\Images
     */
    public function make()
    {
        // Add registered image sizes.
        $this->addImages();

        // Add sizes to the media attachment settings dropdown list.
        $this->filter->add('image_size_names_choose', [$this, 'addImagesToDropDownList']);

        return $this;
    }

    /**
     * Loop through the registered image sizes and add them.
     */
    protected function addImages()
    {
        foreach ($this->data as $slug => $properties) {
            list($width, $height, $crop) = $properties;
            add_image_size($slug, $width, $height, $crop);
        }
    }

    /**
     * Add image sizes to the media size dropdown list.
     *
     * @param array $sizes The existing sizes.
     *
     * @return array
     */
    public function addImagesToDropDownList(array $sizes)
    {
        $new = [];

        foreach ($this->data as $slug => $properties) {
            // If no 4th option, stop the loop.
            if (4 !== count($properties)) {
                break;
            }

            // Grab last property
            $show = array_pop($properties);

            // Allow true or string value.
            // If string, use it as display name.
            if ($show) {
                if (is_string($show)) {
                    $new[$slug] = __($show, THEMOSIS_FRAMEWORK_TEXTDOMAIN);
                } else {
                    $new[$slug] = __($this->label($slug), THEMOSIS_FRAMEWORK_TEXTDOMAIN);
                }
            }
        }

        return array_merge($sizes, $new);
    }

    /**
     * Clean the image slug for display.
     * Remove '-', '_' and set first character to uppercase.
     *
     * @param string $text The text to clean.
     *
     * @return string
     */
    protected function label($text)
    {
        return ucwords(str_replace(['-', '_'], ' ', $text));
    }
}
