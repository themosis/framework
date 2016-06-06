<?php

class SupportTest extends PHPUnit_Framework_TestCase
{
    public function testSupportsAreRegistered()
    {
        $supports = new \Themosis\Config\Support([
            'post-thumbnail'    => ['post'],
            'my-feature',
            'another-feature'   => 'property-value'
        ]);

        $supports->make();

        $this->assertTrue(current_theme_supports('post-thumbnail'));
        $this->assertTrue(current_theme_supports('my-feature'));
        $this->assertTrue(current_theme_supports('another-feature'));

        // Test properties.
        $this->assertEquals([['post']], get_theme_support('post-thumbnail'));
        $this->assertTrue(is_array(get_theme_support('my-feature')));
        $this->assertEquals([[]], get_theme_support('my-feature'));
        $this->assertEquals(['property-value'],get_theme_support('another-feature'));
    }
}