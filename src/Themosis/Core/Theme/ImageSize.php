<?php

namespace Themosis\Core\Theme;

use Themosis\Hook\IHook;

class ImageSize
{
    /**
     * The user defined image sizes.
     *
     * @var array
     */
    protected $sizes;

    /**
     * @var IHook
     */
    protected $filter;

    public function __construct(array $sizes, IHook $filter)
    {
        $this->sizes = $this->parse($sizes);
        $this->filter = $filter;
    }

    /**
     * Parse the images sizes.
     *
     * @param array $sizes
     *
     * @return array
     */
    protected function parse(array $sizes)
    {
        $images = [];

        foreach ($sizes as $slug => $properties) {
            list($width, $height, $crop, $label) = $this->parseProperties($properties, $slug);

            $images[$slug] = [
                'width' => $width,
                'height' => $height,
                'crop' => $crop,
                'label' => $label
            ];
        }

        return $images;
    }

    /**
     * Parse image properties.
     *
     * @param array  $properties
     * @param string $slug
     *
     * @return array
     */
    protected function parseProperties(array $properties, string $slug)
    {
        switch (count($properties)) {
            case 1:
                // Square with defaults.
                return [$properties[0], $properties[0], false, false];
                break;
            case 2:
                // Custom size with defaults.
                return [$properties[0], $properties[1], false, false];
                break;
            case 3:
                // Custom size with custom crop option.
                return [$properties[0], $properties[1], $properties[2], false];
                break;
            case 4:
            default:
                // All properties with custom label.
                $label = (is_bool($properties[3]) && true === $properties[3]) ? $this->label($slug) : $properties[3];

                return [$properties[0], $properties[1], $properties[2], $label];
        }
    }

    /**
     * Format label for display.
     *
     * @param string $label
     *
     * @return string
     */
    protected function label(string $label)
    {
        return ucwords(str_replace(['-', '_'], ' ', $label));
    }

    /**
     * Return the defined images sizes.
     *
     * @return array
     */
    public function getSizes()
    {
        return $this->sizes;
    }

    /**
     * Register theme image sizes.
     *
     * @return $this
     */
    public function register()
    {
        if (function_exists('add_image_size')) {
            foreach ($this->sizes as $slug => $props) {
                add_image_size($slug, $props['width'], $props['height'], $props['crop']);
            }
        }

        if (function_exists('add_filter')) {
            $this->filter->add('image_size_names_choose', [$this, 'addToDropDown']);
        }

        return $this;
    }

    /**
     * Filter media size drop down options. Add user custom image sizes.
     *
     * @param array $options
     *
     * @return array
     */
    public function addToDropDown(array $options)
    {
        foreach ($this->sizes as $slug => $props) {
            if ($props['label'] && ! isset($options[$slug])) {
                $options[$slug] = $props['label'];
            }
        }

        return $options;
    }
}
