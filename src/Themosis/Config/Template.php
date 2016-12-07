<?php

namespace Themosis\Config;

use Themosis\Hook\IHook;

class Template
{
    /**
     * A list of given templates.
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
     * Init the page template module.
     *
     * @return \Themosis\Config\Template
     */
    public function make()
    {
        // Group templates per post_type.
        $all_templates = $this->getTemplates($this->data);

        // Loop templates per $post_type and register templates per $post_type.
        foreach ($all_templates as $post_type => $templates) {
            $this->filter->add("theme_{$post_type}_templates", function($registeredTemplates) use ($templates)
            {
                return array_merge($registeredTemplates, $templates);
            });
        }

        return $this;
    }

    /**
     * Return a formatted array of templates
     *
     * @param array $default The default templates data.
     *
     * @return array The converted templates data.
     */
    protected function getTemplates(array $default)
    {
        // By default, there is always page templates.
        $templates = [
            'page' => []
        ];

        foreach ($default as $slug => $properties) {

            // 1 - $slug is int -> meaning it's only for pages.
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
                if (is_array($properties) && !empty($properties)) {
                    // 2.2.1 - $properties has only one value, meaning it's a display name and only
                    // available to page.
                    if (1 === count($properties) && is_string($properties[0])) {
                        $templates['page'][$slug] = $properties[0];
                    }

                    // 2.2.2 - $properties has 2 values
                    if (2 === count($properties)) {
                        // 2.2.2.1 - Loop through the second one (cast it as array in case of).
                        $post_types = (array) $properties[1];

                        foreach ($post_types as $post_type) {
                            $post_type = trim($post_type);

                            // A - Verify if $post_type exists. If not, add it.
                            if (!isset($templates[$post_type])) {
                                $templates[$post_type] = [];
                            }

                            // B - At this point, there is a $post_type in the $templates.
                            // Basically, only add your templates to each one of them.
                            $templates[$post_type][$slug] = is_string($properties[0]) ? trim($properties[0]) : $this->formatName($slug);
                        }
                    }
                }
            }
        }

        return $templates;
    }

    /**
     * Format template slug key into a template display name.
     *
     * @param string $name The default template slug key name.
     *
     * @return string
     */
    protected function formatName($name)
    {
        return str_replace(['-', '_'], ' ', ucfirst(trim($name)));
    }

    /**
     * Get the template names data and return them.
     *
     * @deprecated
     *
     * @return array An array of template names.
     */
    protected function names()
    {
        $names = [];

        foreach ($this->data as $key => $value) {
            if (is_int($key)) {
                $names[$value] = str_replace(['-', '_'], ' ', ucfirst(trim($value)));
            } else {
                $names[$key] = $value;
            }
        }

        return $names;
    }
}
