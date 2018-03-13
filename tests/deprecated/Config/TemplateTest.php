<?php

use PHPUnit\Framework\TestCase;

class TemplateTest extends TestCase
{
    public function testTemplatesAreRegistered()
    {
        $templates = new \Themosis\Config\Template([
            'contact',
            'about' => 'About Us',
            'team',
            'managing-orders' => 'Orders',
        ], new \Themosis\Hook\FilterBuilder(new \Themosis\Foundation\Application()));

        $templates->make();

        // Check templates are registered.
        // Cannot test this...???
    }

    public function testConvertConfigArrayIntoGroupOfTemplates()
    {
        // Data retrieved from a config file.
        $data = [
            'full-width',
            'custom-template' => 'Custom template',
            'two-columns' => ['Two Columns', ['page', 'jl_cars']],
            'large-header' => ['Large Header', 'jl_cars'],
            'thumbnails' => ['Thumbnails'],
            'list' => ['List', ['page']]
        ];

        $templates = $this->getTemplates($data);

        $expected = [
            'page' => [
                'full-width' => 'Full width',
                'custom-template' => 'Custom template',
                'two-columns' => 'Two Columns',
                'thumbnails' => 'Thumbnails',
                'list' => 'List'
            ],
            'jl_cars' => [
                'two-columns' => 'Two Columns',
                'large-header' => 'Large Header'
            ]
        ];

        $this->assertSame($expected, $templates);
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
                $templates['page'][$properties] = str_replace(['-', '_'], ' ', ucfirst(trim($properties)));
            } else {
                // 2 - (associative array) $slug is a string and we're dealing with $properties.
                // 2.1 - $properties is a string only, so the template is only available to page.
                if (is_string($properties)) {
                    $templates['page'][$slug] = $properties;
                }

                // 2.2 - $properties is an array.
                if (is_array($properties) && ! empty($properties)) {
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
                            if (! isset($templates[$post_type])) {
                                $templates[$post_type] = [];
                            }

                            // B - At this point, there is a $post_type in the $templates.
                            // Basically, only add your templates to each one of them.
                            $templates[$post_type][$slug] = is_string($properties[0]) ? trim($properties[0]) : str_replace(['-', '_'], ' ', ucfirst(trim($slug)));
                        }
                    }
                }
            }
        }

        return $templates;
    }
}
