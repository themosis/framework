<?php

namespace Themosis\Config;

class Menu
{
    /**
     * Save the menus list.
     *
     * @var array
     */
    protected $data = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Register the menus.
     *
     * @return \Themosis\Config\Menu
     */
    public function make()
    {
        if (is_array($this->data) && !empty($this->data)) {
            $locations = [];

            foreach ($this->data as $slug => $desc) {
                $locations[$slug] = $desc;
            }

            register_nav_menus($locations);
        }

        return $this;
    }
}
