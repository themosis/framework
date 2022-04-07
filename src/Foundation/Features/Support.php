<?php

namespace Themosis\Foundation\Features;

use InvalidArgumentException;

class Support
{
    protected array $features;

    protected array $mustHaveProperties = [
        'custom-background',
        'custom-header',
        'html5',
        'post-formats',
        'post-thumbnails',
        'starter-content'
    ];

    public function __construct(array $features)
    {
        $this->features = $this->parse($features);
    }

    /**
     * Parse theme features.
     */
    protected function parse(array $features): array
    {
        $allowed = [];

        foreach ($features as $feature => $value) {
            if (is_int($feature)) {
                /**
                 * Allow theme features without options.
                 * Though post formats must be added with properties.
                 * Here we first check that "post-formats" is not provided
                 * without a value. If so, just pass it.
                 */
                if (! in_array($value, $this->mustHaveProperties, true)) {
                    $allowed[$value] = $value;
                } else {
                    throw new InvalidArgumentException(
                        "The theme feature [$value] must have a defined property in order to work."
                    );
                }
            } else {
                $allowed[$feature] = $value;
            }
        }

        return $allowed;
    }

    /**
     * Register theme support.
     */
    public function register(): void
    {
        if (! function_exists('add_theme_support') || empty($this->features)) {
            return;
        }

        foreach ($this->features as $feature => $value) {
            add_theme_support($feature, $value);
        }
    }
}
