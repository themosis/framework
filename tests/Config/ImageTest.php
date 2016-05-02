<?php


class ImageTest extends PHPUnit_Framework_TestCase
{
    public function testImageConfig()
    {
        $images = new \Themosis\Config\Images([
            'my-image-size' => [200, 400, true, true],
            'some-size'     => [550, 250, false, 'Custom Name']
        ]);
        
        $images->make();

        // Check if registered.
        $this->assertTrue(has_image_size('my-image-size'));
        $this->assertTrue(has_image_size('some-size'));
        
        // Check sizes are correctly defined.
        global $_wp_additional_image_sizes;

        $sample1 = $_wp_additional_image_sizes['my-image-size'];
        $this->assertEquals(200, $sample1['width']);
        $this->assertEquals(400, $sample1['height']);
        $this->assertTrue($sample1['crop']);
        
        // Check if 'Custom Name' is defined.
        $instance = $this;
        add_filter('image_size_names_choose', function($sizes) use ($instance)
        {
            $instance->assertTrue(isset($sizes['some-size']));
            $instance->assertEquals($sizes['some-size'], 'Custom Name');
            return $sizes;
        }, 12);

        apply_filters('image_size_names_choose', []);
    }
}