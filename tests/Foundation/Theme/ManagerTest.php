<?php

namespace Themosis\Tests\Foundation\Theme;

use Composer\Autoload\ClassLoader;
use Themosis\Foundation\Theme\Manager;
use Themosis\Tests\Installers\WordPressConfiguration;
use Themosis\Tests\TestCase;

class ManagerTest extends TestCase
{
    private Manager $manager;

    private WordPressConfiguration $configuration;

    protected function setUp(): void
    {
        parent::setUp();

        $manager = new Manager($this->app, new ClassLoader(), $this->app['config']);
        $manager->load(WP_CONTENT_DIR . '/themes/themosis-fake-theme');

        $this->manager = $manager;

        $this->configuration = $this->app->make(WordPressConfiguration::class);
    }

    /** @test */
    public function it_can_extract_theme_directory_name(): void
    {
        $this->assertEquals($this->configuration->defaultTheme(), $this->manager->getDirectory());
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
        $path = WP_CONTENT_DIR . '/themes/' . $this->configuration->defaultTheme();

        $this->assertEquals($path, $this->manager->getPath());
        $this->assertEquals($path . '/config', $this->manager->getPath('config'));
    }

    /** @test */
    public function it_can_return_theme_url(): void
    {
        $themePath = 'https://themosis.test/content/themes/' . $this->configuration->defaultTheme();

        $this->assertEquals($themePath, $this->manager->getUrl());
        $this->assertEquals($themePath . '/config/theme.php', $this->manager->getUrl('config/theme.php'));
    }

    /** @test */
    public function it_can_load_theme_configuration(): void
    {
        $config = $this->app['config'];

        $this->assertIsArray($config['theme']);
        $this->assertTrue($config['theme.autoload']);
        $this->assertIsArray($config['theme.sizes']);
        $this->assertEquals('medium', $config['theme.sizes.md']);
        $this->assertTrue($config['misc.access']);
    }

    /** @test */
    public function it_can_define_theme_textdomain_constant(): void
    {
        $this->assertTrue(defined('THEME_TD'));
        $this->assertEquals($this->manager->getHeader('text_domain'), THEME_TD);
    }

    /** @test */
    public function it_can_register_custom_image_sizes(): void
    {
        /**
         * @todo Write image sizes test.
         */
        $this->markTestSkipped();
    }

    /** @test */
    public function it_can_register_theme_service_providers(): void
    {
        /**
         * @todo Write service providers test.
         */
        $this->markTestSkipped();
    }

    /** @test */
    public function it_can_register_theme_views(): void
    {
        /**
         * @todo Write views test.
         */
        $this->markTestSkipped();
    }

    /** @test */
    public function it_can_locate_theme_assets(): void
    {
        /**
         * @todo Write theme asset location test.
         */
        $this->markTestSkipped();
    }

    /** @test */
    public function it_can_register_theme_navigation_menus(): void
    {
        /**
         * @todo Write menus registration test.
         */
        $this->markTestSkipped();
    }

    /** @test */
    public function it_can_register_theme_sidebars(): void
    {
        /**
         * @todo Write sidebars test.
         */
        $this->markTestSkipped();
    }

    /** @test */
    public function it_can_register_theme_support(): void
    {
        /**
         * @todo Write theme support test.
         */
        $this->markTestSkipped();
    }

    /** @test */
    public function it_can_register_theme_templates(): void
    {
        /**
         * @todo Write theme templates test.
         */
        $this->markTestSkipped();
    }
}
