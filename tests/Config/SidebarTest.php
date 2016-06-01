<?php

class SidebarTest extends PHPUnit_Framework_TestCase
{
    public function testSidebarsAreRegistered()
    {
        $sidebars = new \Themosis\Config\Sidebar([
            [
                'name' => 'My Sidebar',
                'id' => 'my-sidebar',
                'description' => 'Area of my sidebar',
                'before_widget' => '<div>',
                'after_widget' => '</div>',
                'before_title' => '<h2>',
                'after_title' => '</h2>',
            ],
            [
                'name' => 'Blog Sidebar',
                'id' => 'blog-sidebar',
                'description' => 'Area of the blog sidebar'
            ]
        ]);

        $sidebars->make();

        $this->assertTrue(is_registered_sidebar('my-sidebar'));
        $this->assertTrue(is_registered_sidebar('blog-sidebar'));
    }
}
