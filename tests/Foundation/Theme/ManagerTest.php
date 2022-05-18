<?php

namespace Themosis\Tests\Foundation\Theme;

use Composer\Autoload\ClassLoader;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Factory;
use Illuminate\View\ViewServiceProvider;
use Theme\Models\FakeModel;
use Theme\ThemeHelper;
use Themosis\Foundation\Theme\Manager;
use Themosis\Hook\Filter;
use Themosis\Tests\Installers\WordPressConfiguration;
use Themosis\Tests\TestCase;

class ManagerTest extends TestCase
{
    private Manager $manager;

    private WordPressConfiguration $configuration;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make('config')->set('view.paths', []);

        $this->app->register(FilesystemServiceProvider::class);
        $this->app->register(ViewServiceProvider::class);

        $manager = new Manager($this->app, new ClassLoader(), $this->app['config'], new Filter($this->app));
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
        $this->assertTrue($config['theme.active']);
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
    public function it_can_autoload_theme_classes(): void
    {
        $this->assertInstanceOf(ThemeHelper::class, new ThemeHelper());
        $this->assertInstanceOf(FakeModel::class, new FakeModel());
    }

    /** @test */
    public function it_can_register_custom_image_sizes(): void
    {
        $imageSizes = [
            'square' => [250, 250, true],
            'landscape' => [640, 480, false, true],
            'portrait' => [480, 640, false, 'Portrait Image Size'],
        ];

        $this->manager->images($imageSizes);

        $this->assertTrue(has_image_size('square'));
        $this->assertTrue(has_image_size('landscape'));
        $this->assertTrue(has_image_size('portrait'));

        $this->assertEquals(250, wp_get_additional_image_sizes()['square']['width']);
        $this->assertEquals(250, wp_get_additional_image_sizes()['square']['height']);
        $this->assertTrue(wp_get_additional_image_sizes()['square']['crop']);

        $this->assertEquals(640, wp_get_additional_image_sizes()['landscape']['width']);
        $this->assertEquals(480, wp_get_additional_image_sizes()['landscape']['height']);
        $this->assertFalse(wp_get_additional_image_sizes()['landscape']['crop']);

        $this->assertEquals(480, wp_get_additional_image_sizes()['portrait']['width']);
        $this->assertEquals(640, wp_get_additional_image_sizes()['portrait']['height']);
        $this->assertFalse(wp_get_additional_image_sizes()['portrait']['crop']);

        (new Filter($this->app))->add('image_size_names_choose', function (array $sizes) use ($imageSizes) {
            $this->assertTrue(isset($sizes['landscape']));
            $this->assertEquals('Landscape', $sizes['landscape']);

            $this->assertTrue(isset($sizes['portrait']));
            $this->assertEquals($imageSizes['portrait'][3], $sizes['portrait']);
        });

        (new Filter($this->app))->run('image_size_names_choose', []);
    }

    /** @test */
    public function it_can_register_theme_service_providers(): void
    {
        $this->manager->providers([
            TestThemeServiceProvider::class,
            MenuServiceProvider::class,
        ]);

        $this->assertInstanceOf(TestThemeServiceProvider::class, $this->app->getProvider(TestThemeServiceProvider::class));
        $this->assertInstanceOf(MenuServiceProvider::class, $this->app->getProvider(MenuServiceProvider::class));

        $this->assertTrue($this->app->bound('test-theme'));
        $this->assertTrue($this->app->make('test-theme'));
    }

    /** @test */
    public function it_can_register_theme_views(): void
    {
        $this->manager->views([
            'views',
        ]);

        /** @var Factory $factory */
        $factory = $this->app->make('view');

        $this->assertEquals(
            $this->app->basePath('tests/application/public/content/themes/themosis-fake-theme/views/main.blade.php'),
            $factory->getFinder()->find('main'),
        );
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

class TestThemeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind('test-theme', function () {
            return true;
        });
    }
}

class MenuServiceProvider extends ServiceProvider
{
}
