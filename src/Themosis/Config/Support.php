<?php

namespace Themosis\Config;

class Support
{
    /**
     * List of theme supports.
     */
    protected $data = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Add theme supports.
     * Call this only into your theme functions.php file or files relative to it.
     * If call outside of the theme, wrap this with `after_setup_theme` hook.
     *
     * @return \Themosis\Config\Support;
     */
    public function make()
    {
        if (is_array($this->data) && !empty($this->data)) {
            foreach ($this->data as $feature => $value) {
                // Allow theme features without options.
                if (is_int($feature)) {
                    // Post formats must be added with properties in order to work correctly.
                    // Here we check if it has been defined without properties.
                    // If there are no properties, it's not added.
                    if ('post-formats' !== $value) {
                        //add_theme_support($value);
                        $this->support($value);
                    }
                } else {
                    // Theme features with options.
                    //add_theme_support($feature, $value);
                    $this->support($feature, $value);
                }
            }
        }

        return $this;
    }

    /**
     * Add theme support.
     *
     * @param string $feature
     * @param array  $value
     */
    protected function support($feature, $value = [])
    {
	    add_theme_support($feature, $value);
    }
}
