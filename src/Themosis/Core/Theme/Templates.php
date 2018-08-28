<?php

namespace Themosis\Core\Theme;

use Themosis\Hook\IHook;

class Templates
{
    /**
     * @var array
     */
    protected $templates;

    /**
     * @var IHook
     */
    protected $filter;

    public function __construct(array $options, IHook $filter)
    {
        $this->templates = $this->parse($options);
        $this->filter = $filter;
    }

    /**
     * Parse templates.
     *
     * @param array $options
     *
     * @return array
     */
    protected function parse(array $options)
    {
        $templates = [
            'page' => []
        ];

        foreach ($options as $slug => $properties) {
            // 1 - $slug is int -> meaning it's only for pages
            // and $properties is the slug name.
            if (is_int($slug)) {
                $templates['page'][$properties] = $this->formatName($properties);
            } else {
                // 2 - (associative array) $slug is a string and we're dealing with $properties.
                // 2.1 - $properties is a string only, so the template is only available to page.
                if (is_string($properties)) {
                    $templates['page'][$slug] = $properties;
                }

                // 2.2 - $properties is an array.
                if (is_array($properties) && ! empty($properties)) {
                    // 2.2.1 - $properties has only one value, meaning it's a display name
                    //and only available to page.
                    if (1 === count($properties) && is_string($properties[0])) {
                        $templates['page'][$slug] = $properties[0];
                    }

                    // 2.2.2 - $properties has 2 values
                    if (2 === count($properties)) {
                        // 2.2.2.1 - Loop through the second one (cast it as array in case of).
                        $post_types = (array) $properties[1];

                        foreach ($post_types as $post_type) {
                            $post_type = trim($post_type);

                            // Verify if $post_type exists. If not, add it with a default value.
                            if (! isset($templates[$post_type])) {
                                $templates[$post_type] = [];
                            }

                            // The is a $post_type in the $templates.
                            // Basically, add the templates to each one of them.
                            $templates[$post_type][$slug] = is_string($properties[0])
                                ? trim($properties[0])
                                : $this->formatName($slug);
                        }
                    }
                }
            }
        }

        return $templates;
    }

    /**
     * Format the template name.
     *
     * @param string $name
     *
     * @return string
     */
    protected function formatName(string $name)
    {
        return ucwords(trim(str_replace(['-', '_'], ' ', $name)));
    }

    /**
     * Register theme templates.
     */
    public function register()
    {
        foreach ($this->templates as $post_type => $templates) {
            if (empty($templates)) {
                continue;
            }

            $this->filter->add("theme_{$post_type}_templates", function ($registered) use ($templates) {
                return array_merge($registered, $templates);
            });
        }
    }
}
