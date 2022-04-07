<?php

namespace Themosis\Foundation\Features;

use Themosis\Hook\IHook;

class ImageSize
{
    protected array $sizes;

    protected IHook $filter;

    public function __construct(array $sizes, IHook $filter)
    {
        $this->sizes = $this->parse($sizes);
        $this->filter = $filter;
    }

    protected function parse(array $sizes): array
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

    protected function parseProperties(array $properties, string $slug): array
    {
        return match(count($properties)) {
            /**
             * Square with defaults.
             */
            1 => [$properties[0], $properties[0], false, false],

            /**
             * Custom size with defaults.
             */
            2 => [$properties[0], $properties[1], false, false],

            /**
             * Custom size with custom crop option.
             */
            3 => [$properties[0], $properties[1], $properties[2], false],

            /**
             * All properties with custom label.
             */
            default => [
                $properties[0],
                $properties[1],
                $properties[2],
                (true === $properties[3]) ? $this->label($slug) : $properties[3]
            ]
        };
    }

    /**
     * Format label for display.
     */
    protected function label(string $label): string
    {
        return ucwords(str_replace(['-', '_'], ' ', $label));
    }

    /**
     * Return the defined image sizes.
     */
    public function getSizes(): array
    {
        return $this->sizes;
    }

    /**
     * Register theme image sizes.
     */
    public function register(): self
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
     * Media size drop down options filter callback.
     */
    public function addToDropDown(array $options): array
    {
        foreach ($this->sizes as $slug => $props) {
            if ($props['label'] && ! isset($options[$slug])) {
                $options[$slug] = $props['label'];
            }
        }

        return $options;
    }
}
