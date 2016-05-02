<?php

class TemplateTest extends PHPUnit_Framework_TestCase
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
}
