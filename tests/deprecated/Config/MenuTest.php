<?php

use PHPUnit\Framework\TestCase;

class MenuTest extends TestCase
{
    public function testMenusAreRegistered()
    {
        $menus = new \Themosis\Config\Menu([
            'header-nav'    => 'Header navigation menu description.',
            'footer-nav'    => 'Footer description.',
            'secondary-nav' => 'Secondary navigation description.'
        ]);

        $menus->make();

        $registered = get_registered_nav_menus();

        $this->assertTrue(isset($registered['header-nav']));
        $this->assertTrue(isset($registered['footer-nav']));
        $this->assertTrue(isset($registered['secondary-nav']));
    }
}
