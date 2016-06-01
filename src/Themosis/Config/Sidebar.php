<?php

namespace Themosis\Config;

class Sidebar
{
    /**
     * Save list of sidebars.
     */
    protected $data = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Register the sidebars.
     *
     * @return \Themosis\Config\Sidebar
     */
    public function make()
    {
        if (is_array($this->data) && !empty($this->data)) {
            foreach ($this->data as $sidebar) {
                register_sidebar($sidebar);
            }
        }

        return $this;
    }
}
