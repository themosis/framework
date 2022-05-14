<?php

namespace Themosis\Tests\Foundation\Theme;

use Composer\Autoload\ClassLoader;
use Themosis\Foundation\Theme\Manager;
use Themosis\Tests\TestCase;

class ManagerTest extends TestCase
{
    private Manager $manager;

    protected function setUp(): void
    {
        parent::setUp();

        $manager = new Manager($this->app, new ClassLoader(), $this->app['config']);
        $manager->load(WP_CONTENT_DIR . '/themes/themosis-fake-theme');

        $this->manager = $manager;
    }

    /** @test */
    public function it_can_extract_theme_directory_name(): void
    {
        $this->assertEquals('themosis-fake-theme', $this->manager->getDirectory());
    }

    /** @test */
    public function it_can_load_a_theme_headers(): void
    {
        $this->assertEquals('Themosis Fake Theme', $this->manager->getHeader('name'));
        $this->assertEquals('Themosis', $this->manager->getHeader('author'));
        $this->assertEquals('1.0.0', $this->manager->getHeader('version'));
        $this->assertEquals('fake-domain', $this->manager->getHeader('text_domain'));

        $this->assertNull($this->manager->getHeader('custom_property'));
    }

    /**  @test */
    public function it_can_return_theme_paths(): void
    {
        $path = WP_CONTENT_DIR . '/themes/themosis-fake-theme';

        $this->assertEquals($path, $this->manager->getPath());
        $this->assertEquals($path . '/config', $this->manager->getPath('config'));
    }

    /** @test */
    public function it_can_return_theme_url(): void
    {
        $themePath = 'https://themosis.test/content/themes/themosis-fake-theme';

        $this->assertEquals($themePath, $this->manager->getUrl());
        $this->assertEquals($themePath . '/config/theme.php', $this->manager->getUrl('config/theme.php'));
    }
}
