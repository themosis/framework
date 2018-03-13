<?php

use PHPUnit\Framework\TestCase;

class LoaderTest extends TestCase
{
    public function testLoader()
    {
        $loader = new \Themosis\Load\Loader();

        // Add files.
        $loader->add([
            themosis_path('core').'tests/Load/files'
        ]);

        $loader->load();

        $files = $loader->getFiles();

        $this->assertTrue(count($files) === 4);
        $this->assertEquals('actions', $files[0]['name']);
        $this->assertEquals('application', $files[1]['name']);
        $this->assertEquals('theme', $files[2]['name']);
        $this->assertEquals('woocommerce', $files[3]['name']);

        // Check code from theme.php file is loaded - Proof of file included.
        $this->assertTrue(function_exists('themosis_theme_test_file_is_loaded'));
        $this->assertTrue(themosis_theme_test_file_is_loaded());
    }

    public function testWidgetLoader()
    {
        $loader = new Themosis\Load\WidgetLoader(new \Themosis\Hook\FilterBuilder(new \Themosis\Foundation\Application()));

        $loader->add([
            themosis_path('core').'tests/Load/widgets'
        ]);

        $loader->load();

        $files = $loader->getFiles();

        $this->assertTrue(count($files) === 2);
        $this->assertTrue(count($loader->getWidgets()) === 2);
        $this->assertEquals(['AbcWidget_Widget', 'MyWidget_Widget'], $loader->getWidgets());
    }
}
